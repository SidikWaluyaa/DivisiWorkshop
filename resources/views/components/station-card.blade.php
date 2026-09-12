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

    // SLA Calculations for Fast Track (Cumulative from created_at)
    $isPrepSlaViolated = $order->isPrepSlaViolated();
    $isSortirSlaViolated = $order->isSortirSlaViolated();
    $isProdSlaViolated = $order->isProductionSlaViolated();
    $isQcSlaViolated = $order->isQcSlaViolated();

    // Resolved CX Issue
    $resolvedIssue = $order->cxIssues ? $order->cxIssues->where('status', 'RESOLVED')->sortByDesc('resolved_at')->first() : null;

    // Revision badge & history
    $revisions = $order->revisions ? $order->revisions->whereIn('qc_stage', ['PRODUKSI', 'AKHIR']) : collect();
    $revisionCount = $revisions->count();
    $revisionBadgeClass = match(true) {
        $revisionCount >= 3 => 'bg-red-600',
        $revisionCount == 2 => 'bg-orange-500',
        $revisionCount >= 1 => 'bg-yellow-500',
        default => '',
    };

    // Initialize technician variables for card drawer
    $techId = $isPrepReview || !isset($order->{$byColumn}) ? null : $order->{$byColumn};
    $techName = $isPrepReview || !$techByRelation || !isset($order->{$techByRelation}) ? null : ($order->{$techByRelation}->name ?? null);
    $startedAt = $isPrepReview || !isset($order->{$startedAtColumn}) ? null : $order->{$startedAtColumn};
@endphp

<tbody {{ $attributes }} x-data="{ 
          expanded: false,
          showPhotos: false, 
          showFinishModal: false, 
          finishType: '',
          finishDate: '{{ now()->format('Y-m-d\TH:i') }}',
          isHighlighted: false,
          showOverrideModal: false,
          overrideReason: '',
          pendingTechId: null,
          pendingType: null,
          pendingOrderId: null,
          pendingCurrentTechName: '',
          pendingNewTechName: '',
          pendingStationLabel: '',
          openOverrideModal(orderId, stationType, techId, currentTechName, newTechName, stationLabel) {
              this.pendingOrderId = orderId;
              this.pendingType = stationType;
              this.pendingTechId = techId;
              this.pendingCurrentTechName = currentTechName;
              this.pendingNewTechName = newTechName;
              this.pendingStationLabel = stationLabel;
              this.overrideReason = '';
              this.showOverrideModal = true;
          },
          submitOverride() {
              if (!this.overrideReason || this.overrideReason.trim().length < 5) {
                  alert('Alasan override wajib diisi minimal 5 karakter.');
                  return;
              }
              $wire.updateTechnicianWithReason(this.pendingOrderId, this.pendingType, this.pendingTechId, this.overrideReason);
              this.showOverrideModal = false;
              this.overrideReason = '';
          },
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
                  @if(strpos($type, 'prep_') !== false && $isPrepSlaViolated)
                      <span class="px-2 py-0.5 rounded text-[8px] font-black bg-red-600 text-white tracking-widest shadow-sm animate-pulse">
                          ⚠️ SLA PREP OVERDUE (TERLAMBAT {{ $order->getDaysInPrep() - 1 }} HARI)
                      </span>
                  @endif
                  @if($isSortirSlaViolated)
                      <span class="px-2 py-0.5 rounded text-[8px] font-black bg-red-600 text-white tracking-widest shadow-sm animate-pulse">
                          ⚠️ SLA SORTIR OVERDUE (TERLAMBAT {{ $order->getDaysInSortir() - 3 }} HARI)
                      </span>
                  @endif
                  @if(strpos($type, 'prod_') !== false && $isProdSlaViolated)
                      <span class="px-2 py-0.5 rounded text-[8px] font-black bg-red-600 text-white tracking-widest shadow-sm animate-pulse">
                          ⚠️ SLA PROD OVERDUE (TERLAMBAT {{ $order->getDaysInProduction() - 4 }} HARI)
                      </span>
                  @endif
                  @if(strpos($type, 'qc_') !== false && $isQcSlaViolated)
                      <span class="px-2 py-0.5 rounded text-[8px] font-black bg-red-600 text-white tracking-widest shadow-sm animate-pulse">
                          ⚠️ SLA QC OVERDUE (TERLAMBAT {{ $order->getDaysInQc() - 1 }} HARI)
                      </span>
                  @endif
                 @if($order->fast_track_status === 'yes')
                     <span class="px-2 py-0.5 rounded text-[8px] font-black bg-orange-600 text-white tracking-widest shadow-sm animate-pulse">
                         FAST TRACK
                     </span>
                 @endif
                 @if($resolvedIssue)
                     <span class="px-2 py-0.5 rounded text-[8px] font-black bg-emerald-600 text-white tracking-widest shadow-xs inline-flex items-center gap-1" title="Laporan kendala CX telah diselesaikan (Resolved)">
                         <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                         CX RESOLVED
                     </span>
                 @endif
                 @if($revisionCount > 0)
                     <span class="px-2 py-0.5 rounded text-[8px] font-black {{ $revisionBadgeClass }} text-white tracking-widest shadow-sm"
                           title="SPK ini pernah direvisi sebanyak {{ $revisionCount }}x">
                         REVISI {{ $revisionCount }}x
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
                 $displayPriority = $priority === 'OTO' ? 'Prioritas' : $priority;
                 $isUrgent = in_array($priority, ['Prioritas', 'Urgent', 'Express', 'OTO', 'Prioritas/Urgent']);
             @endphp
             <span class="inline-flex items-center text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider
                          {{ $isUrgent 
                             ? 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400 border border-red-200 dark:border-red-900/40 animate-pulse' 
                             : 'bg-gray-105 text-gray-700 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700' }}">
                 @if($isUrgent)
                     🔥 {{ $displayPriority }}
                 @else
                     ⚡ {{ $displayPriority }}
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
                                  'jahit' => ['label' => 'QC Jahit', 'by' => 'qcJahitBy', 'col' => 'qc_jahit_completed_at'],
                              ];
                          } else {
                              $stations = [
                                  'treatment' => ['label' => 'Treatment', 'by' => 'prodCleaningBy', 'col' => 'prod_cleaning_completed_at'],
                                  'cleanup' => ['label' => 'QC Cleanup', 'by' => 'qcCleanupBy', 'col' => 'qc_cleanup_completed_at'],
                                  'final' => ['label' => 'QC Final', 'by' => 'qcFinalBy', 'col' => 'qc_final_completed_at'],
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
                       @php
                           $isProdApproved = false;
                           $activeSj = null;
                           if (str_starts_with($type, 'prod')) {
                               $isProdApproved = $order->logs->where('step', 'PRODUCTION')->where('action', 'PRODUCTION_APPROVED')->isNotEmpty() 
                                   || $order->current_location === 'Produksi (Siap Handover)';
                               $activeSj = $order->suratJalanItems?->first(fn($item) => $item->suratJalan && $item->suratJalan->jenis_serah_terima === 'produksi_to_post_qc')?->suratJalan;
                           }
                       @endphp

                       @if($isProdApproved || $activeSj)
                           @if($activeSj)
                               <a href="{{ route('surat-jalan.show', $activeSj->id) }}" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-all shadow-xs"
                                  title="Lihat Surat Jalan: {{ $activeSj->nomor_surat }}">
                                   <span>🚚</span>
                                   <span>SJ #{{ $activeSj->nomor_surat }}</span>
                               </a>
                           @else
                               <a href="{{ route('surat-jalan.index', ['jenis' => 'produksi_to_post_qc', 'search' => $order->spk_number]) }}" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-all shadow-xs"
                                  title="SPK Sudah Disetujui. Siap dibuatkan atau dimasukkan ke Surat Jalan Handover QC">
                                   <span>✅</span>
                                   <span>Siap Handover QC</span>
                               </a>
                           @endif
                       @else
                           <button wire:click="performApprove({{ $order->id }})" 
                                   wire:confirm="{{ str_starts_with($type, 'prep') ? 'Preparation sudah OK semua? Lanjut ke Sortir?' : (str_starts_with($type, 'prod') ? 'Sudah dicek dan OK? Lanjut ke QC?' : 'QC Akhir sudah OK semua? Order akan masuk Staging Outbound.') }}"
                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-1 shadow transition-all active:scale-95">
                               {{ str_starts_with($type, 'qc') ? 'Lolos QC' : 'Approve' }}
                           </button>

                           <button @click="$dispatch('open-revision-modal', { id: {{ $order->id }}, number: '{{ $order->spk_number }}' })" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-bold text-xs transition-all active:scale-95 cursor-pointer">
                               Revisi
                           </button>
                       @endif

                      <button @click="expanded = !expanded" class="p-1.5 rounded-lg hover:bg-teal-50 dark:hover:bg-gray-750 text-teal-600 dark:text-teal-400 transition-colors">
                          <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transform transition-transform duration-250" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                          </svg>
                      </button>
                  </div>
              </td>
          @else
              @if(str_starts_with($type, 'prep_') && !$isReviewTab)
                  {{-- Column 5: Progress Tugas Prep --}}
                  <td class="px-6 py-4" @click.stop>
                      <div class="flex flex-col gap-1.5 min-w-[200px]">
                          {{-- Washing (Cuci) --}}
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 dark:text-teal-400 uppercase">Cuci:</span>
                              @if($order->isStationUnneeded('prep_washing'))
                                  <div class="flex items-center gap-1.5">
                                      <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[9px] font-black rounded uppercase">TIDAK PERLU</span>
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   $wire.updateTechnician({{ $order->id }}, 'prep_washing', techId);
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="text-[9px] font-bold text-teal-600 dark:text-teal-400 hover:underline px-1 py-0.5 rounded cursor-pointer"
                                                  title="Aktifkan kembali atau pilih teknisi">
                                              Ubah
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Cuci:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @forelse($this->techs['washing'] ?? [] as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-200 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-950/40 rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                              </svg>
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  </div>
                              @elseif($order->prep_washing_completed_at)
                                  <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->prep_washing_completed_at->format('d M H:i') }}">✓ {{ $order->prepWashingBy->name ?? 'Selesai' }}</span>
                              @else
                                  <div class="flex items-center gap-1">
                                      @php
                                          $washingStarted = (bool)$order->prep_washing_started_at;
                                          $washingCurrentTech = $order->prepWashingBy->name ?? 'Kosong';
                                          $washingTechList = collect($this->techs['washing'] ?? []);
                                          if ($order->prep_washing_by && $order->prepWashingBy && !$washingTechList->contains('id', $order->prep_washing_by) && !str_contains($order->prepWashingBy->name, 'Dr. Shoe')) {
                                              $washingTechList->push($order->prepWashingBy);
                                          }
                                      @endphp

                                      {{-- Custom Picker Trigger Button & Teleported Popover --}}
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   @if($washingStarted)
                                                       openOverrideModal({{ $order->id }}, 'prep_washing', techId, '{{ addslashes($washingCurrentTech) }}', techName, 'Cuci');
                                                   @else
                                                       $wire.updateTechnician({{ $order->id }}, 'prep_washing', techId);
                                                   @endif
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->prep_washing_by ? 'bg-teal-50/70 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800 hover:bg-teal-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                                  title="Pilih teknisi Cuci">
                                              <div class="flex items-center gap-1 truncate">
                                                  @if($order->prepWashingBy)
                                                      <span class="w-3.5 h-3.5 rounded-full bg-teal-200 dark:bg-teal-900 text-teal-800 dark:text-teal-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                          {{ substr($order->prepWashingBy->name, 0, 1) }}
                                                      </span>
                                                      <span class="truncate">{{ $order->prepWashingBy->name }}</span>
                                                  @else
                                                      <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                                  @endif
                                              </div>
                                              <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                              </svg>
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Cuci:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                      <button type="button" 
                                                              @click="selectTech('none', 'Tidak Diperlukan')" 
                                                              class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                          <span class="text-xs">🚫</span>
                                                          <span>Tidak Diperlukan</span>
                                                      </button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @if($order->prep_washing_by)
                                                          <button type="button" 
                                                                  @click="selectTech('', 'Kosongkan')" 
                                                                  class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                              -- Kosongkan Pilihan --
                                                          </button>
                                                      @endif

                                                      @forelse($washingTechList as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prep_washing_by == $t->id ? 'text-teal-600 bg-teal-50 dark:bg-teal-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->prep_washing_by == $t->id)
                                                                  <svg class="w-3 h-3 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  </div>
                              @endif
                          </div>

                          {{-- Sol Prep --}}
                          @if($order->needs_prep_sol || $order->isStationUnneeded('prep_sol'))
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-orange-500 uppercase">Sol:</span>
                              @if($order->isStationUnneeded('prep_sol'))
                                  <div class="flex items-center gap-1.5">
                                      <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[9px] font-black rounded uppercase">TIDAK PERLU</span>
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   $wire.updateTechnician({{ $order->id }}, 'prep_sol', techId);
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="text-[9px] font-bold text-orange-600 dark:text-orange-400 hover:underline px-1 py-0.5 rounded cursor-pointer"
                                                  title="Aktifkan kembali atau pilih teknisi">
                                              Ubah
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Sol Prep:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @forelse($this->techs['sol'] ?? [] as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-200 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                              </svg>
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  </div>
                              @elseif($order->prep_sol_completed_at)
                                  <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->prep_sol_completed_at->format('d M H:i') }}">✓ {{ $order->prepSolBy->name ?? '-' }}</span>
                              @else
                                  <div class="flex items-center gap-1">
                                      @php
                                          $solStarted = (bool)$order->prep_sol_started_at;
                                          $solCurrentTech = $order->prepSolBy->name ?? 'Kosong';
                                          $solTechList = collect($this->techs['sol'] ?? []);
                                          if ($order->prep_sol_by && $order->prepSolBy && !$solTechList->contains('id', $order->prep_sol_by) && !str_contains($order->prepSolBy->name, 'Dr. Shoe')) {
                                              $solTechList->push($order->prepSolBy);
                                          }
                                      @endphp

                                      {{-- Custom Picker Trigger Button & Teleported Popover --}}
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   @if($solStarted)
                                                       openOverrideModal({{ $order->id }}, 'prep_sol', techId, '{{ addslashes($solCurrentTech) }}', techName, 'Sol');
                                                   @else
                                                       $wire.updateTechnician({{ $order->id }}, 'prep_sol', techId);
                                                   @endif
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->prep_sol_by ? 'bg-orange-50/70 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-800 hover:bg-orange-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                                  title="Pilih teknisi Sol Prep">
                                              <div class="flex items-center gap-1 truncate">
                                                  @if($order->prepSolBy)
                                                      <span class="w-3.5 h-3.5 rounded-full bg-orange-200 dark:bg-orange-900 text-orange-800 dark:text-orange-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                          {{ substr($order->prepSolBy->name, 0, 1) }}
                                                      </span>
                                                      <span class="truncate">{{ $order->prepSolBy->name }}</span>
                                                  @else
                                                      <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                                  @endif
                                              </div>
                                              <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                              </svg>
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Sol:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                      <button type="button" 
                                                              @click="selectTech('none', 'Tidak Diperlukan')" 
                                                              class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                          <span class="text-xs">🚫</span>
                                                          <span>Tidak Diperlukan</span>
                                                      </button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @if($order->prep_sol_by)
                                                          <button type="button" 
                                                                  @click="selectTech('', 'Kosongkan')" 
                                                                  class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                              -- Kosongkan Pilihan --
                                                          </button>
                                                      @endif

                                                      @forelse($solTechList as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prep_sol_by == $t->id ? 'text-orange-600 bg-orange-50 dark:bg-orange-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->prep_sol_by == $t->id)
                                                                  <svg class="w-3 h-3 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  </div>
                              @endif
                          </div>
                          @endif

                          {{-- Upper Prep --}}
                          @if($order->needs_prep_upper || $order->isStationUnneeded('prep_upper'))
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-purple-500 uppercase">Upper:</span>
                              @if($order->isStationUnneeded('prep_upper'))
                                  <div class="flex items-center gap-1.5">
                                      <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[9px] font-black rounded uppercase">TIDAK PERLU</span>
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   $wire.updateTechnician({{ $order->id }}, 'prep_upper', techId);
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="text-[9px] font-bold text-purple-600 dark:text-purple-400 hover:underline px-1 py-0.5 rounded cursor-pointer"
                                                  title="Aktifkan kembali atau pilih teknisi">
                                              Ubah
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Upper Prep:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @forelse($this->techs['upper'] ?? [] as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/40 rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                              </svg>
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  </div>
                              @elseif($order->prep_upper_completed_at)
                                  <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->prep_upper_completed_at->format('d M H:i') }}">✓ {{ $order->prepUpperBy->name ?? '-' }}</span>
                              @else
                                  <div class="flex items-center gap-1">
                                      @php
                                          $upperStarted = (bool)$order->prep_upper_started_at;
                                          $upperCurrentTech = $order->prepUpperBy->name ?? 'Kosong';
                                          $upperTechList = collect($this->techs['upper'] ?? []);
                                          if ($order->prep_upper_by && $order->prepUpperBy && !$upperTechList->contains('id', $order->prep_upper_by) && !str_contains($order->prepUpperBy->name, 'Dr. Shoe')) {
                                              $upperTechList->push($order->prepUpperBy);
                                          }
                                      @endphp

                                      {{-- Custom Picker Trigger Button & Teleported Popover --}}
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   @if($upperStarted)
                                                       openOverrideModal({{ $order->id }}, 'prep_upper', techId, '{{ addslashes($upperCurrentTech) }}', techName, 'Upper');
                                                   @else
                                                       $wire.updateTechnician({{ $order->id }}, 'prep_upper', techId);
                                                   @endif
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->prep_upper_by ? 'bg-purple-50/70 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800 hover:bg-purple-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                                  title="Pilih teknisi Upper Prep">
                                              <div class="flex items-center gap-1 truncate">
                                                  @if($order->prepUpperBy)
                                                      <span class="w-3.5 h-3.5 rounded-full bg-purple-200 dark:bg-purple-900 text-purple-800 dark:text-purple-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                          {{ substr($order->prepUpperBy->name, 0, 1) }}
                                                      </span>
                                                      <span class="truncate">{{ $order->prepUpperBy->name }}</span>
                                                  @else
                                                      <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                                  @endif
                                              </div>
                                              <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                              </svg>
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Upper:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                      <button type="button" 
                                                              @click="selectTech('none', 'Tidak Diperlukan')" 
                                                              class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                          <span class="text-xs">🚫</span>
                                                          <span>Tidak Diperlukan</span>
                                                      </button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @if($order->prep_upper_by)
                                                          <button type="button" 
                                                                  @click="selectTech('', 'Kosongkan')" 
                                                                  class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                              -- Kosongkan Pilihan --
                                                          </button>
                                                      @endif

                                                      @forelse($upperTechList as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prep_upper_by == $t->id ? 'text-purple-600 bg-purple-50 dark:bg-purple-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->prep_upper_by == $t->id)
                                                                  <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  </div>
                              @endif
                          </div>
                          @endif
                      </div>
                  </td>
                  {{-- Column 6: Duration / SLA --}}
                  <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                      <div class="text-[11px] font-semibold text-gray-750 dark:text-gray-300">
                          @if($order->estimation_date)
                              <span class="text-orange-600 font-bold" title="Estimasi Selesai">{{ $order->estimation_date->format('d M Y') }}</span>
                          @else
                              <span class="text-gray-400">-</span>
                          @endif
                      </div>
                  </td>
              @elseif($type === 'prod_reparasi')
                  {{-- Column 5: Progress Tugas Produksi Terpadu (Upper -> Soling -> QC Jahit) --}}
                  @php
                      $hasUpper = $order->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'upper') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'upper'));
                      $hasSol = $order->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'sol') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'sol'));
                      $hasJahit = $hasSol || $hasUpper || $order->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'jahit') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'jahit'));
                      
                      if (!$hasUpper && !$hasSol && !$hasJahit) {
                          $hasUpper = true;
                      }

                      $isSolLocked = $hasUpper && !$order->prod_upper_completed_at && !$order->isStationUnneeded('prod_upper');
                      $isJahitLocked = ($hasUpper && !$order->prod_upper_completed_at && !$order->isStationUnneeded('prod_upper')) || ($hasSol && !$order->prod_sol_completed_at && !$order->isStationUnneeded('prod_sol'));
                  @endphp
                  <td class="px-6 py-4" @click.stop>
                      <div class="flex flex-col gap-1.5 min-w-[220px]">
                          {{-- 1. Upper --}}
                          @if(!$hasUpper || $order->isStationUnneeded('prod_upper'))
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-purple-600 uppercase">Upper:</span>
                              <div class="flex items-center gap-1.5">
                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700 shadow-2xs">
                                      <span>🚫</span> Tidak Diperlukan
                                  </span>
                                  @if($hasUpper)
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   const hiddenInput = document.getElementById('tech-prod_upper-{{ $order->id }}');
                                                   if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                                   $wire.updateTechnician({{ $order->id }}, 'prod_upper', techId);
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <input type="hidden" id="tech-prod_upper-{{ $order->id }}" value="{{ $order->prod_upper_by }}">

                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[8px] font-extrabold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/50 border border-purple-200/60 dark:border-purple-800 hover:bg-purple-100/60 transition-all cursor-pointer shadow-2xs"
                                                  title="Ubah Teknisi Upper">
                                              <span>Ubah</span>
                                              <svg class="w-2 h-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                              </svg>
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Upper:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                      <button type="button" 
                                                              @click="selectTech('none', 'Tidak Diperlukan')" 
                                                              class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                          <span class="text-xs">🚫</span>
                                                          <span>Tidak Diperlukan</span>
                                                      </button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @if($order->prod_upper_by)
                                                          <button type="button" 
                                                                  @click="selectTech('', 'Kosong')" 
                                                                  class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                              -- Kosongkan Pilihan --
                                                          </button>
                                                      @endif

                                                      @forelse($this->techs['upper'] ?? [] as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prod_upper_by == $t->id ? 'text-purple-600 bg-purple-50 dark:bg-purple-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->prod_upper_by == $t->id)
                                                                  <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  @endif
                              </div>
                          </div>
                          @elseif($order->prod_upper_completed_at)
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-purple-600 uppercase">Upper:</span>
                              <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->prod_upper_completed_at->format('d M H:i') }}">Selesai: {{ $order->prodUpperBy->name ?? '-' }}</span>
                          </div>
                          @else
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-purple-600 uppercase">Upper:</span>
                              <div class="flex items-center gap-1.5">
                                  @php
                                      $upperStarted = (bool)$order->prod_upper_started_at;
                                      $upperCurrentTech = $order->prodUpperBy->name ?? 'Kosong';
                                  @endphp
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 250;
                                               let left = rect.left;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               if (left < 10) left = 10;
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           },
                                           selectTech(techId, techName) {
                                               this.open = false;
                                               const hiddenInput = document.getElementById('tech-prod_upper-{{ $order->id }}');
                                               if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                               @if($upperStarted)
                                                   openOverrideModal({{ $order->id }}, 'prod_upper', techId, '{{ addslashes($upperCurrentTech) }}', techName, 'Upper');
                                               @else
                                                   $wire.updateTechnician({{ $order->id }}, 'prod_upper', techId);
                                               @endif
                                           }
                                       }" 
                                       @scroll.window="open = false"
                                       @click.stop>
                                      
                                      <input type="hidden" id="tech-prod_upper-{{ $order->id }}" value="{{ $order->prod_upper_by }}">

                                      <button type="button" 
                                              @click="toggle($event)"
                                              class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->prod_upper_by ? 'bg-purple-50/70 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800 hover:bg-purple-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                              title="Pilih teknisi Upper">
                                          <div class="flex items-center gap-1 truncate">
                                              @if($order->prodUpperBy)
                                                  <span class="w-3.5 h-3.5 rounded-full bg-purple-200 dark:bg-purple-900 text-purple-800 dark:text-purple-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                      {{ substr($order->prodUpperBy->name, 0, 1) }}
                                                  </span>
                                                  <span class="truncate">{{ $order->prodUpperBy->name }}</span>
                                              @else
                                                  <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                              @endif
                                          </div>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi Upper:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>

                                              <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                  <button type="button" 
                                                          @click="selectTech('none', 'Tidak Diperlukan')" 
                                                          class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                      <span class="text-xs">🚫</span>
                                                      <span>Tidak Diperlukan</span>
                                                  </button>
                                              </div>

                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @if($order->prod_upper_by)
                                                      <button type="button" 
                                                              @click="selectTech('', 'Kosong')" 
                                                              class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                          -- Kosongkan Pilihan --
                                                      </button>
                                                  @endif

                                                  @forelse($this->techs['upper'] ?? [] as $t)
                                                      <button type="button" 
                                                              @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prod_upper_by == $t->id ? 'text-purple-600 bg-purple-50 dark:bg-purple-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                          <div class="flex items-center gap-1.5 truncate">
                                                              <span class="w-4 h-4 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                  {{ substr($t->name, 0, 1) }}
                                                              </span>
                                                              <span class="truncate">{{ $t->name }}</span>
                                                          </div>
                                                          @if($order->prod_upper_by == $t->id)
                                                              <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                              </svg>
                                                          @endif
                                                      </button>
                                                  @empty
                                                      <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                  @endforelse
                                              </div>
                                          </div>
                                      </template>
                                  </div>

                                  @if($order->prod_upper_started_at)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'prod_upper', 'finish');" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-700 px-2.5 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer" title="Selesaikan Upper">Selesaikan</button>
                                  @elseif($order->prod_upper_by)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'prod_upper', 'start', {{ $order->prod_upper_by ?? 'null' }});" class="text-[10px] font-bold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer" title="Mulai Upper">Mulai</button>
                                  @endif
                              </div>
                          </div>
                          @endif

                          {{-- 2. Soling --}}
                          @if(!$hasSol || $order->isStationUnneeded('prod_sol'))
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-orange-500 uppercase">Soling:</span>
                              <div class="flex items-center gap-1.5">
                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700 shadow-2xs">
                                      <span>🚫</span> Tidak Diperlukan
                                  </span>
                                  @if($hasSol)
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   const hiddenInput = document.getElementById('tech-prod_sol-{{ $order->id }}');
                                                   if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                                   $wire.updateTechnician({{ $order->id }}, 'prod_sol', techId);
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <input type="hidden" id="tech-prod_sol-{{ $order->id }}" value="{{ $order->prod_sol_by }}">

                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[8px] font-extrabold text-orange-700 dark:text-orange-300 bg-orange-50 dark:bg-orange-950/50 border border-orange-200/60 dark:border-orange-800 hover:bg-orange-100/60 transition-all cursor-pointer shadow-2xs"
                                                  title="Ubah Teknisi Soling">
                                              <span>Ubah</span>
                                              <svg class="w-2 h-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                              </svg>
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi Soling:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                      <button type="button" 
                                                              @click="selectTech('none', 'Tidak Diperlukan')" 
                                                              class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                          <span class="text-xs">🚫</span>
                                                          <span>Tidak Diperlukan</span>
                                                      </button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @if($order->prod_sol_by)
                                                          <button type="button" 
                                                                  @click="selectTech('', 'Kosong')" 
                                                                  class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                              -- Kosongkan Pilihan --
                                                          </button>
                                                      @endif

                                                      @forelse($this->techs['sol'] ?? [] as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prod_sol_by == $t->id ? 'text-orange-600 bg-orange-50 dark:bg-orange-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->prod_sol_by == $t->id)
                                                                  <svg class="w-3 h-3 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  @endif
                              </div>
                          </div>
                          @elseif($order->prod_sol_completed_at)
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-orange-500 uppercase">Soling:</span>
                              <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->prod_sol_completed_at->format('d M H:i') }}">Selesai: {{ $order->prodSolBy->name ?? '-' }}</span>
                          </div>
                          @elseif($isSolLocked)
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-orange-500 uppercase">Soling:</span>
                              <span class="text-yellow-600 italic text-[10px]" title="Menunggu Upper selesai">Menunggu Upper</span>
                          </div>
                          @else
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-orange-500 uppercase">Soling:</span>
                              <div class="flex items-center gap-1.5">
                                  @php
                                      $solStarted = (bool)$order->prod_sol_started_at;
                                      $solCurrentTech = $order->prodSolBy->name ?? 'Kosong';
                                  @endphp
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 250;
                                               let left = rect.left;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               if (left < 10) left = 10;
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           },
                                           selectTech(techId, techName) {
                                               this.open = false;
                                               const hiddenInput = document.getElementById('tech-prod_sol-{{ $order->id }}');
                                               if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                               @if($solStarted)
                                                   openOverrideModal({{ $order->id }}, 'prod_sol', techId, '{{ addslashes($solCurrentTech) }}', techName, 'Soling');
                                               @else
                                                   $wire.updateTechnician({{ $order->id }}, 'prod_sol', techId);
                                               @endif
                                           }
                                       }" 
                                       @scroll.window="open = false"
                                       @click.stop>
                                      
                                      <input type="hidden" id="tech-prod_sol-{{ $order->id }}" value="{{ $order->prod_sol_by }}">

                                      <button type="button" 
                                              @click="toggle($event)"
                                              class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->prod_sol_by ? 'bg-orange-50/70 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-800 hover:bg-orange-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                              title="Pilih teknisi Soling">
                                          <div class="flex items-center gap-1 truncate">
                                              @if($order->prodSolBy)
                                                  <span class="w-3.5 h-3.5 rounded-full bg-orange-200 dark:bg-orange-900 text-orange-800 dark:text-orange-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                      {{ substr($order->prodSolBy->name, 0, 1) }}
                                                  </span>
                                                  <span class="truncate">{{ $order->prodSolBy->name }}</span>
                                              @else
                                                  <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                              @endif
                                          </div>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi Soling:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>

                                              <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                  <button type="button" 
                                                          @click="selectTech('none', 'Tidak Diperlukan')" 
                                                          class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                      <span class="text-xs">🚫</span>
                                                      <span>Tidak Diperlukan</span>
                                                  </button>
                                              </div>

                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @if($order->prod_sol_by)
                                                      <button type="button" 
                                                              @click="selectTech('', 'Kosong')" 
                                                              class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                          -- Kosongkan Pilihan --
                                                      </button>
                                                  @endif

                                                  @forelse($this->techs['sol'] ?? [] as $t)
                                                      <button type="button" 
                                                              @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prod_sol_by == $t->id ? 'text-orange-600 bg-orange-50 dark:bg-orange-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->prod_sol_by == $t->id)
                                                                  <svg class="w-3 h-3 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>

                                      @if($order->prod_sol_started_at)
                                          <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'prod_sol', 'finish');" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-700 px-2.5 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer" title="Selesaikan Soling">Selesaikan</button>
                                      @elseif($order->prod_sol_by)
                                          <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'prod_sol', 'start', {{ $order->prod_sol_by ?? 'null' }});" class="text-[10px] font-bold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer" title="Mulai Soling">Mulai</button>
                                      @endif
                              </div>
                          </div>
                          @endif

                          {{-- 3. QC Jahit --}}
                          @if(!$hasJahit || $order->isStationUnneeded('qc_jahit'))
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-blue-600 uppercase">QC Jahit:</span>
                              <div class="flex items-center gap-1.5">
                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700 shadow-2xs">
                                      <span>🚫</span> Tidak Diperlukan
                                  </span>
                                  @if($hasJahit)
                                      <div class="relative" 
                                           x-data="{ 
                                               open: false, 
                                               dropup: false, 
                                               topPos: 'auto', 
                                               bottomPos: 'auto', 
                                               leftPos: 0,
                                               toggle(event) {
                                                   const rect = event.currentTarget.getBoundingClientRect();
                                                   const popoverWidth = 185;
                                                   const popoverHeight = 250;
                                                   let left = rect.left;
                                                   if (left + popoverWidth > window.innerWidth - 10) {
                                                       left = window.innerWidth - popoverWidth - 10;
                                                   }
                                                   if (left < 10) left = 10;
                                                   this.leftPos = left;

                                                   const spaceBelow = window.innerHeight - rect.bottom;
                                                   if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                       this.dropup = true;
                                                       this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                       this.topPos = 'auto';
                                                   } else {
                                                       this.dropup = false;
                                                       this.topPos = (rect.bottom + 4) + 'px';
                                                       this.bottomPos = 'auto';
                                                   }
                                                   this.open = !this.open;
                                               },
                                               selectTech(techId, techName) {
                                                   this.open = false;
                                                   const hiddenInput = document.getElementById('tech-qc_jahit-{{ $order->id }}');
                                                   if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                                   $wire.updateTechnician({{ $order->id }}, 'qc_jahit', techId);
                                               }
                                           }" 
                                           @scroll.window="open = false"
                                           @click.stop>
                                          
                                          <input type="hidden" id="tech-qc_jahit-{{ $order->id }}" value="{{ $order->qc_jahit_by }}">

                                          <button type="button" 
                                                  @click="toggle($event)"
                                                  class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[8px] font-extrabold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/50 border border-blue-200/60 dark:border-blue-800 hover:bg-blue-100/60 transition-all cursor-pointer shadow-2xs"
                                                  title="Ubah Teknisi QC Jahit">
                                              <span>Ubah</span>
                                              <svg class="w-2 h-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                              </svg>
                                          </button>

                                          <template x-teleport="body">
                                              <div x-show="open" 
                                                   @click.away="open = false" 
                                                   x-transition:enter="transition ease-out duration-150"
                                                   x-transition:enter-start="opacity-0 scale-95"
                                                   x-transition:enter-end="opacity-100 scale-100"
                                                   x-transition:leave="transition ease-in duration-100"
                                                   x-transition:leave-start="opacity-100 scale-100"
                                                   x-transition:leave-end="opacity-0 scale-95"
                                                   :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                                   class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                                   style="display: none;">
                                                  
                                                  <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                      <span>Pilih Teknisi QC Jahit:</span>
                                                      <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                                  </div>

                                                  <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                      <button type="button" 
                                                              @click="selectTech('none', 'Tidak Diperlukan')" 
                                                              class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                          <span class="text-xs">🚫</span>
                                                          <span>Tidak Diperlukan</span>
                                                      </button>
                                                  </div>

                                                  <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                      @if($order->qc_jahit_by)
                                                          <button type="button" 
                                                                  @click="selectTech('', 'Kosong')" 
                                                                  class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                              -- Kosongkan Pilihan --
                                                          </button>
                                                      @endif

                                                      @forelse($this->techs['jahit'] ?? ($this->techs['all'] ?? []) as $t)
                                                          <button type="button" 
                                                                  @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                                  class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->qc_jahit_by == $t->id ? 'text-blue-600 bg-blue-50 dark:bg-blue-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->qc_jahit_by == $t->id)
                                                                  <svg class="w-3 h-3 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>
                                  @endif
                              </div>
                          </div>
                          @elseif($order->qc_jahit_completed_at)
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-blue-600 uppercase">QC Jahit:</span>
                              <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->qc_jahit_completed_at->format('d M H:i') }}">Selesai: {{ $order->qcJahitBy->name ?? '-' }}</span>
                          </div>
                          @elseif($isJahitLocked)
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-blue-600 uppercase">QC Jahit:</span>
                              <span class="text-yellow-600 italic text-[10px]" title="Menunggu pengerjaan konstruksi selesai">Menunggu Urutan</span>
                          </div>
                          @else
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-blue-600 uppercase">QC Jahit:</span>
                              <div class="flex items-center gap-1.5">
                                  @php
                                      $jahitStarted = (bool)$order->qc_jahit_started_at;
                                      $jahitCurrentTech = $order->qcJahitBy->name ?? 'Kosong';
                                  @endphp
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 250;
                                               let left = rect.left;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               if (left < 10) left = 10;
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           },
                                           selectTech(techId, techName) {
                                               this.open = false;
                                               const hiddenInput = document.getElementById('tech-qc_jahit-{{ $order->id }}');
                                               if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                               @if($jahitStarted)
                                                   openOverrideModal({{ $order->id }}, 'qc_jahit', techId, '{{ addslashes($jahitCurrentTech) }}', techName, 'QC Jahit');
                                               @else
                                                   $wire.updateTechnician({{ $order->id }}, 'qc_jahit', techId);
                                               @endif
                                           }
                                       }" 
                                       @scroll.window="open = false"
                                       @click.stop>
                                      
                                      <input type="hidden" id="tech-qc_jahit-{{ $order->id }}" value="{{ $order->qc_jahit_by }}">

                                      <button type="button" 
                                              @click="toggle($event)"
                                              class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->qc_jahit_by ? 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800 hover:bg-blue-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                              title="Pilih teknisi QC Jahit">
                                          <div class="flex items-center gap-1 truncate">
                                              @if($order->qcJahitBy)
                                                  <span class="w-3.5 h-3.5 rounded-full bg-blue-200 dark:bg-blue-900 text-blue-800 dark:text-blue-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                      {{ substr($order->qcJahitBy->name, 0, 1) }}
                                                  </span>
                                                  <span class="truncate">{{ $order->qcJahitBy->name }}</span>
                                              @else
                                                  <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                              @endif
                                          </div>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi QC Jahit:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>

                                              <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                  <button type="button" 
                                                          @click="selectTech('none', 'Tidak Diperlukan')" 
                                                          class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                      <span class="text-xs">🚫</span>
                                                      <span>Tidak Diperlukan</span>
                                                  </button>
                                              </div>

                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @if($order->qc_jahit_by)
                                                      <button type="button" 
                                                              @click="selectTech('', 'Kosong')" 
                                                              class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                          -- Kosongkan Pilihan --
                                                      </button>
                                                  @endif

                                                  @forelse($this->techs['jahit'] ?? ($this->techs['all'] ?? []) as $t)
                                                      <button type="button" 
                                                              @click="selectTech('{{ $t->id }}', '{{ addslashes($t->name) }}')" 
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->qc_jahit_by == $t->id ? 'text-blue-600 bg-blue-50 dark:bg-blue-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                              <div class="flex items-center gap-1.5 truncate">
                                                                  <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                      {{ substr($t->name, 0, 1) }}
                                                                  </span>
                                                                  <span class="truncate">{{ $t->name }}</span>
                                                              </div>
                                                              @if($order->qc_jahit_by == $t->id)
                                                                  <svg class="w-3 h-3 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                  </svg>
                                                              @endif
                                                          </button>
                                                      @empty
                                                          <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                      @endforelse
                                                  </div>
                                              </div>
                                          </template>
                                      </div>

                                      @if($order->qc_jahit_started_at)
                                          <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'qc_jahit', 'finish');" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-700 px-2.5 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer" title="Selesaikan QC Jahit">Selesaikan</button>
                                      @elseif($order->qc_jahit_by)
                                          <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'qc_jahit', 'start', {{ $order->qc_jahit_by ?? 'null' }});" class="text-[10px] font-bold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer" title="Mulai QC Jahit">Mulai</button>
                                      @endif
                              </div>
                          </div>
                          @endif
                      </div>
                  </td>
                  
{{-- Column 6: Duration / SLA --}}
                  <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                      <div class="text-[11px] font-semibold text-gray-750 dark:text-gray-300">
                          @if($order->estimation_date)
                              <span class="text-orange-600 font-bold" title="Estimasi Selesai">{{ $order->estimation_date->format('d M Y') }}</span>
                          @else
                              <span class="text-gray-400">-</span>
                          @endif
                      </div>
                  </td>
              @elseif($type === 'qc_terpadu' || $type === 'qc_qc')
                  {{-- Column 5: Progress Stasiun QC Terpadu (Treatment -> Cleanup -> Final) --}}
                  @php
                      $hasTreatment = $order->workOrderServices->contains(fn($s) => 
                          \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'clean') || 
                          \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'wash') || 
                          \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'treatment') || 
                          \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'repaint') || 
                          \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'whitening') || 
                          \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'clean') || 
                          \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'treatment')
                      );
                  @endphp
                  <td class="px-6 py-4" @click.stop>
                      <div class="flex flex-col gap-1.5 min-w-[240px]">
                          {{-- 1. Treatment --}}
                          @if($order->isStationUnneeded('prod_cleaning'))
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 uppercase">Treatment:</span>
                              <div class="flex items-center gap-1.5">
                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700 shadow-2xs">
                                      <span>🚫</span> Tidak Diperlukan
                                  </span>
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 220;
                                               let left = rect.right - popoverWidth;
                                               if (left < 10) left = 10;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           }
                                       }" 
                                       @scroll.window="open = false"
                                       @click.stop>
                                      <button type="button" 
                                              @click="toggle($event)" 
                                              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 shadow-2xs hover:shadow-xs transition-all duration-200 active:scale-95 cursor-pointer"
                                              title="Ubah penugasan teknisi">
                                          <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                          </svg>
                                          <span>Ubah</span>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi Treatment:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>
                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @forelse($this->techs['treatment'] ?? ($this->techs['all'] ?? []) as $t)
                                                      <button type="button" 
                                                              wire:click="updateTechnician({{ $order->id }}, 'prod_cleaning', '{{ $t->id }}')" 
                                                              @click="open = false"
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-200 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-950/40 rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                          <div class="flex items-center gap-1.5 truncate">
                                                              <span class="w-4 h-4 rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                  {{ substr($t->name, 0, 1) }}
                                                              </span>
                                                              <span class="truncate">{{ $t->name }}</span>
                                                          </div>
                                                          <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                          </svg>
                                                      </button>
                                                  @empty
                                                      <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                  @endforelse
                                              </div>
                                          </div>
                                      </template>
                                  </div>
                              </div>
                          </div>
                          @elseif(!$hasTreatment)
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 uppercase">Treatment:</span>
                              <span class="text-gray-400 text-[10px] italic">Tidak Diperlukan</span>
                          </div>
                          @elseif($order->prod_cleaning_completed_at)
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 uppercase">Treatment:</span>
                              <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->prod_cleaning_completed_at->format('d M H:i') }}">✓ {{ $order->prodCleaningBy->name ?? '-' }}</span>
                          </div>
                          @else
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 uppercase">Treatment:</span>
                              <div class="flex items-center gap-1">
                                  {{-- Custom Picker Trigger Button & Teleported Popover --}}
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 250;
                                               let left = rect.left;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               if (left < 10) left = 10;
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           },
                                           selectTech(techId) {
                                                this.open = false;
                                                const hiddenInput = document.getElementById('tech-prod_cleaning-{{ $order->id }}');
                                                if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                                $wire.updateTechnician({{ $order->id }}, 'prod_cleaning', techId);
                                            }
                                        }" 
                                        @scroll.window="open = false"
                                        @click.stop>
                                       
                                       <input type="hidden" id="tech-prod_cleaning-{{ $order->id }}" value="{{ $order->prod_cleaning_by }}">

                                       <button type="button" 
                                               @click="toggle($event)""
                                              class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->prod_cleaning_by ? 'bg-teal-50/70 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800 hover:bg-teal-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                              title="Pilih teknisi Treatment">
                                          <div class="flex items-center gap-1 truncate">
                                              @if($order->prodCleaningBy)
                                                  <span class="w-3.5 h-3.5 rounded-full bg-teal-200 dark:bg-teal-900 text-teal-800 dark:text-teal-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                      {{ substr($order->prodCleaningBy->name, 0, 1) }}
                                                  </span>
                                                  <span class="truncate">{{ $order->prodCleaningBy->name }}</span>
                                              @else
                                                  <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                              @endif
                                          </div>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi Treatment:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>

                                              <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                  <button type="button" 
                                                          @click="selectTech('none')" 
                                                          class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                      <span class="text-xs">🚫</span>
                                                      <span>Tidak Diperlukan</span>
                                                  </button>
                                              </div>

                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @if($order->prod_cleaning_by)
                                                      <button type="button" 
                                                              @click="selectTech('')" 
                                                              class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                          -- Kosongkan Pilihan --
                                                      </button>
                                                  @endif

                                                  @forelse($this->techs['treatment'] ?? ($this->techs['all'] ?? []) as $t)
                                                      <button type="button" 
                                                              @click="selectTech('{{ $t->id }}')" 
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->prod_cleaning_by == $t->id ? 'text-teal-600 bg-teal-50 dark:bg-teal-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                          <div class="flex items-center gap-1.5 truncate">
                                                              <span class="w-4 h-4 rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                  {{ substr($t->name, 0, 1) }}
                                                              </span>
                                                              <span class="truncate">{{ $t->name }}</span>
                                                          </div>
                                                          @if($order->prod_cleaning_by == $t->id)
                                                              <svg class="w-3 h-3 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                              </svg>
                                                          @endif
                                                      </button>
                                                  @empty
                                                      <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                  @endforelse
                                              </div>
                                          </div>
                                      </template>
                                  </div>

                                  @if($order->prod_cleaning_started_at)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'prod_cleaning', 'finish');" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-700 px-2 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer">Selesai</button>
                                  @elseif($order->prod_cleaning_by)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'prod_cleaning', 'start', {{ $order->prod_cleaning_by ?? 'null' }});" class="text-[10px] font-bold text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer">Mulai</button>
                                  @endif
                              </div>
                          </div>
                          @endif

                          {{-- 2. QC Cleanup --}}
                          @if($order->isStationUnneeded('qc_cleanup'))
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 uppercase">Cleanup:</span>
                              <div class="flex items-center gap-1.5">
                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700 shadow-2xs">
                                      <span>🚫</span> Tidak Diperlukan
                                  </span>
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 220;
                                               let left = rect.right - popoverWidth;
                                               if (left < 10) left = 10;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           }
                                       }" 
                                       @scroll.window="open = false"
                                       @click.stop>
                                      <button type="button" 
                                              @click="toggle($event)" 
                                              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 shadow-2xs hover:shadow-xs transition-all duration-200 active:scale-95 cursor-pointer"
                                              title="Ubah penugasan teknisi">
                                          <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                          </svg>
                                          <span>Ubah</span>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi Cleanup:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>
                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @forelse($this->techs['cleanup'] ?? [] as $t)
                                                      <button type="button" 
                                                              wire:click="updateTechnician({{ $order->id }}, 'qc_cleanup', '{{ $t->id }}')" 
                                                              @click="open = false"
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-200 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-950/40 rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                          <div class="flex items-center gap-1.5 truncate">
                                                              <span class="w-4 h-4 rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                  {{ substr($t->name, 0, 1) }}
                                                              </span>
                                                              <span class="truncate">{{ $t->name }}</span>
                                                          </div>
                                                          <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                          </svg>
                                                      </button>
                                                  @empty
                                                      <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                  @endforelse
                                              </div>
                                          </div>
                                      </template>
                                  </div>
                              </div>
                          </div>
                          @elseif($order->qc_cleanup_completed_at)
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 uppercase">Cleanup:</span>
                              <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->qc_cleanup_completed_at->format('d M H:i') }}">✓ {{ $order->qcCleanupBy->name ?? '-' }}</span>
                          </div>
                          @else
                          <div class="flex items-center justify-between text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                              <span class="font-bold text-teal-600 uppercase">Cleanup:</span>
                              <div class="flex items-center gap-1">
                                  {{-- Custom Picker Trigger Button & Teleported Popover --}}
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 250;
                                               let left = rect.left;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               if (left < 10) left = 10;
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           },
                                           selectTech(techId) {
                                                this.open = false;
                                                const hiddenInput = document.getElementById('tech-qc_cleanup-{{ $order->id }}');
                                                if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                                $wire.updateTechnician({{ $order->id }}, 'qc_cleanup', techId);
                                            }
                                        }" 
                                        @scroll.window="open = false"
                                        @click.stop>
                                       
                                       <input type="hidden" id="tech-qc_cleanup-{{ $order->id }}" value="{{ $order->qc_cleanup_by }}">

                                       <button type="button" 
                                               @click="toggle($event)""
                                              class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->qc_cleanup_by ? 'bg-teal-50/70 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800 hover:bg-teal-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                              title="Pilih teknisi Cleanup">
                                          <div class="flex items-center gap-1 truncate">
                                              @if($order->qcCleanupBy)
                                                  <span class="w-3.5 h-3.5 rounded-full bg-teal-200 dark:bg-teal-900 text-teal-800 dark:text-teal-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                      {{ substr($order->qcCleanupBy->name, 0, 1) }}
                                                  </span>
                                                  <span class="truncate">{{ $order->qcCleanupBy->name }}</span>
                                              @else
                                                  <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                              @endif
                                          </div>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi Cleanup:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>

                                              <div class="mb-1 pb-1 border-b border-gray-100 dark:border-gray-700/60">
                                                  <button type="button" 
                                                          @click="selectTech('none')" 
                                                          class="w-full text-left px-2 py-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors flex items-center gap-1.5 group cursor-pointer">
                                                      <span class="text-xs">🚫</span>
                                                      <span>Tidak Diperlukan</span>
                                                  </button>
                                              </div>

                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @if($order->qc_cleanup_by)
                                                      <button type="button" 
                                                              @click="selectTech('')" 
                                                              class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                          -- Kosongkan Pilihan --
                                                      </button>
                                                  @endif

                                                  @forelse($this->techs['cleanup'] ?? [] as $t)
                                                      <button type="button" 
                                                              @click="selectTech('{{ $t->id }}')" 
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->qc_cleanup_by == $t->id ? 'text-teal-600 bg-teal-50 dark:bg-teal-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                          <div class="flex items-center gap-1.5 truncate">
                                                              <span class="w-4 h-4 rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                  {{ substr($t->name, 0, 1) }}
                                                              </span>
                                                              <span class="truncate">{{ $t->name }}</span>
                                                          </div>
                                                          @if($order->qc_cleanup_by == $t->id)
                                                              <svg class="w-3 h-3 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                              </svg>
                                                          @endif
                                                      </button>
                                                  @empty
                                                      <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                  @endforelse
                                              </div>
                                          </div>
                                      </template>
                                  </div>

                                  @if($order->qc_cleanup_started_at)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'qc_cleanup', 'finish');" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-700 px-2 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer">Selesai</button>
                                  @elseif($order->qc_cleanup_by)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'qc_cleanup', 'start', {{ $order->qc_cleanup_by ?? 'null' }});" class="text-[10px] font-bold text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer">Mulai</button>
                                  @endif
                              </div>
                          </div>
                          @endif

                          {{-- 3. QC Final --}}
                          @if($order->isStationUnneeded('qc_final'))
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-emerald-600 uppercase">QC Final:</span>
                              <div class="flex items-center gap-1.5">
                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700 shadow-2xs">
                                      <span>🚫</span> Tidak Diperlukan
                                  </span>
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 220;
                                               let left = rect.right - popoverWidth;
                                               if (left < 10) left = 10;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           }
                                       }" 
                                       @scroll.window="open = false"
                                       @click.stop>
                                      <button type="button" 
                                              @click="toggle($event)" 
                                              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 shadow-2xs hover:shadow-xs transition-all duration-200 active:scale-95 cursor-pointer"
                                              title="Ubah penugasan teknisi">
                                          <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                          </svg>
                                          <span>Ubah</span>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi QC Final:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>
                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @forelse($this->techs['final'] ?? [] as $t)
                                                      <button type="button" 
                                                              wire:click="updateTechnician({{ $order->id }}, 'qc_final', '{{ $t->id }}')" 
                                                              @click="open = false"
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-200 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                          <div class="flex items-center gap-1.5 truncate">
                                                              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                  {{ substr($t->name, 0, 1) }}
                                                              </span>
                                                              <span class="truncate">{{ $t->name }}</span>
                                                          </div>
                                                          <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                          </svg>
                                                      </button>
                                                  @empty
                                                      <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                  @endforelse
                                              </div>
                                          </div>
                                      </template>
                                  </div>
                              </div>
                          </div>
                          @elseif($order->qc_final_completed_at)
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-emerald-600 uppercase">QC Final:</span>
                              <span class="text-green-600 font-bold bg-green-50/50 px-1.5 py-0.5 rounded text-[10px]" title="Selesai: {{ $order->qc_final_completed_at->format('d M H:i') }}">✓ {{ $order->qcFinalBy->name ?? '-' }}</span>
                          </div>
                          @else
                          <div class="flex items-center justify-between text-[11px]">
                              <span class="font-bold text-emerald-600 uppercase">QC Final:</span>
                              <div class="flex items-center gap-1">
                                  {{-- Custom Picker Trigger Button & Teleported Popover (No Tidak Diperlukan since Final is mandatory) --}}
                                  <div class="relative" 
                                       x-data="{ 
                                           open: false, 
                                           dropup: false, 
                                           topPos: 'auto', 
                                           bottomPos: 'auto', 
                                           leftPos: 0,
                                           toggle(event) {
                                               const rect = event.currentTarget.getBoundingClientRect();
                                               const popoverWidth = 185;
                                               const popoverHeight = 250;
                                               let left = rect.left;
                                               if (left + popoverWidth > window.innerWidth - 10) {
                                                   left = window.innerWidth - popoverWidth - 10;
                                               }
                                               if (left < 10) left = 10;
                                               this.leftPos = left;

                                               const spaceBelow = window.innerHeight - rect.bottom;
                                               if (spaceBelow < popoverHeight && rect.top > spaceBelow) {
                                                   this.dropup = true;
                                                   this.bottomPos = (window.innerHeight - rect.top + 4) + 'px';
                                                   this.topPos = 'auto';
                                               } else {
                                                   this.dropup = false;
                                                   this.topPos = (rect.bottom + 4) + 'px';
                                                   this.bottomPos = 'auto';
                                               }
                                               this.open = !this.open;
                                           },
                                           selectTech(techId) {
                                                this.open = false;
                                                const hiddenInput = document.getElementById('tech-qc_final-{{ $order->id }}');
                                                if (hiddenInput) hiddenInput.value = techId === 'none' ? '' : techId;
                                                $wire.updateTechnician({{ $order->id }}, 'qc_final', techId);
                                            }
                                        }" 
                                        @scroll.window="open = false"
                                        @click.stop>
                                       
                                       <input type="hidden" id="tech-qc_final-{{ $order->id }}" value="{{ $order->qc_final_by }}">

                                       <button type="button" 
                                               @click="toggle($event)""
                                              class="inline-flex items-center justify-between gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold transition-all duration-200 cursor-pointer shadow-2xs active:scale-95 border {{ $order->qc_final_by ? 'bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100/60' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-50' }} max-w-[125px]"
                                              title="Pilih teknisi QC Final">
                                          <div class="flex items-center gap-1 truncate">
                                              @if($order->qcFinalBy)
                                                  <span class="w-3.5 h-3.5 rounded-full bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 flex items-center justify-center text-[8px] font-black shrink-0">
                                                      {{ substr($order->qcFinalBy->name, 0, 1) }}
                                                  </span>
                                                  <span class="truncate">{{ $order->qcFinalBy->name }}</span>
                                              @else
                                                  <span class="text-slate-400 italic text-[9px]">Pilih Teknisi</span>
                                              @endif
                                          </div>
                                          <svg class="w-2 h-2 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                          </svg>
                                      </button>

                                      <template x-teleport="body">
                                          <div x-show="open" 
                                               @click.away="open = false" 
                                               x-transition:enter="transition ease-out duration-150"
                                               x-transition:enter-start="opacity-0 scale-95"
                                               x-transition:enter-end="opacity-100 scale-100"
                                               x-transition:leave="transition ease-in duration-100"
                                               x-transition:leave-start="opacity-100 scale-100"
                                               x-transition:leave-end="opacity-0 scale-95"
                                               :style="'position: fixed; z-index: 99999; left: ' + leftPos + 'px; top: ' + topPos + '; bottom: ' + bottomPos + ';'"
                                               class="min-w-[185px] max-w-[220px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-1.5 backdrop-blur-md"
                                               style="display: none;">
                                              
                                              <div class="px-2 py-1 text-[9px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                                                  <span>Pilih Teknisi QC Final:</span>
                                                  <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs leading-none">✕</button>
                                              </div>

                                              <div class="max-h-48 overflow-y-auto space-y-0.5 custom-scrollbar">
                                                  @if($order->qc_final_by)
                                                      <button type="button" 
                                                              @click="selectTech('')" 
                                                              class="w-full text-left px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg transition-colors italic cursor-pointer">
                                                          -- Kosongkan Pilihan --
                                                      </button>
                                                  @endif

                                                  @forelse($this->techs['final'] ?? [] as $t)
                                                      <button type="button" 
                                                              @click="selectTech('{{ $t->id }}')" 
                                                              class="w-full text-left px-2.5 py-1.5 text-[11px] font-bold {{ $order->qc_final_by == $t->id ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 font-black' : 'text-gray-700 dark:text-gray-200 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }} rounded-lg transition-colors flex items-center justify-between group cursor-pointer">
                                                          <div class="flex items-center gap-1.5 truncate">
                                                              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 flex items-center justify-center text-[9px] font-black shrink-0">
                                                                  {{ substr($t->name, 0, 1) }}
                                                              </span>
                                                              <span class="truncate">{{ $t->name }}</span>
                                                          </div>
                                                          @if($order->qc_final_by == $t->id)
                                                              <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                              </svg>
                                                          @endif
                                                      </button>
                                                  @empty
                                                      <div class="px-2 py-1 text-[10px] text-gray-400 italic">Tidak ada teknisi</div>
                                                  @endforelse
                                              </div>
                                          </div>
                                      </template>
                                  </div>

                                  @if($order->qc_final_started_at)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'qc_final', 'finish');" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-700 px-2 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer">Selesai</button>
                                  @elseif($order->qc_final_by)
                                      <button type="button" @click.stop="window.updateStation({{ $order->id }}, 'qc_final', 'start', {{ $order->qc_final_by ?? 'null' }});" class="text-[10px] font-bold text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-md transition-all shadow-xs active:scale-95 cursor-pointer">Mulai</button>
                                  @endif
                              </div>
                          </div>
                          @endif
                      </div>
                  </td>

                  {{-- Column 6: Duration / SLA --}}
                  <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                      <div class="text-[11px] font-semibold text-gray-750 dark:text-gray-300">
                          @if($order->estimation_date)
                              <span class="text-orange-600 font-bold" title="Estimasi Selesai">{{ $order->estimation_date->format('d M Y') }}</span>
                          @else
                              <span class="text-gray-400">-</span>
                          @endif
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
              @endif

              {{-- Column 7: Actions & Master Express Pass --}}
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                      @if($type === 'qc_terpadu' || $type === 'qc_qc')
                          <button type="button" 
                                  @click.stop="$wire.expressPass({{ $order->id }})" 
                                  class="px-3 py-1.5 bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-[11px] rounded-xl shadow-md shadow-amber-500/20 border border-amber-300 transition-all active:scale-95 flex items-center gap-1 cursor-pointer" 
                                  title="1-Klik Lolos QC 3 Tahap Sekaligus & Pindah ke Siap Selesai">
                              ⚡ Loloskan QC
                          </button>
                      @endif
                      <button @click.stop="expanded = !expanded" class="p-1.5 rounded-lg hover:bg-teal-50 dark:hover:bg-gray-750 text-teal-600 dark:text-teal-400 transition-colors">
                          <svg :class="{'rotate-180': expanded}" class="w-5 h-5 transform transition-transform duration-250" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                          </svg>
                      </button>
                  </div>
              </td>
          @endif
     </tr>

     {{-- Collapsible detail row --}}
      <tr x-show="expanded" x-cloak x-transition>
           <td colspan="{{ ($isReviewTab ?? false) ? 6 : 7 }}" class="bg-gray-50 dark:bg-gray-850 p-5 border-t border-b border-gray-200 dark:border-gray-700">
               <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                   
                   {{-- Info & Notes Col --}}
                   <div class="md:col-span-2 space-y-4">
                        {{-- Revision History Panel --}}
                        @if($revisionCount > 0)
                            <div class="p-3.5 rounded-xl border-2 shadow-sm
                                {{ $revisionCount >= 3 ? 'bg-red-50/90 dark:bg-red-950/40 border-red-300 dark:border-red-800' : ($revisionCount == 2 ? 'bg-orange-50/90 dark:bg-orange-950/40 border-orange-300 dark:border-orange-800' : 'bg-yellow-50/90 dark:bg-yellow-950/40 border-yellow-300 dark:border-yellow-800') }}">
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <span class="text-[9px] font-black uppercase tracking-widest
                                        {{ $revisionCount >= 3 ? 'text-red-700 dark:text-red-400' : ($revisionCount == 2 ? 'text-orange-700 dark:text-orange-400' : 'text-yellow-700 dark:text-yellow-400') }}">
                                        Riwayat Revisi ({{ $revisionCount }}x)
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black text-white
                                        {{ $revisionCount >= 3 ? 'bg-red-600' : ($revisionCount == 2 ? 'bg-orange-500' : 'bg-yellow-500') }}">
                                        REVISI {{ $revisionCount }}x
                                    </span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[10px] border-collapse">
                                        <thead>
                                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                                <th class="text-left py-1 pr-3 font-black text-gray-400 uppercase tracking-wider">#</th>
                                                <th class="text-left py-1 pr-3 font-black text-gray-400 uppercase tracking-wider">Asal Revisi</th>
                                                <th class="text-left py-1 pr-3 font-black text-gray-400 uppercase tracking-wider">Stage QC</th>
                                                <th class="text-left py-1 pr-3 font-black text-gray-400 uppercase tracking-wider">Tanggal</th>
                                                <th class="text-right py-1 font-black text-rose-500 uppercase tracking-wider">Est. Kerugian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($revisions->sortBy('created_at') as $idx => $rev)
                                                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                                    <td class="py-1 pr-3 font-bold text-gray-500">{{ $idx + 1 }}</td>
                                                    <td class="py-1 pr-3 font-bold text-gray-700 dark:text-gray-300">
                                                        @php
                                                            $asalLabel = match($rev->origin_status) {
                                                                'PRODUCTION' => 'QC Produksi',
                                                                'POST', 'POST_QC', 'FINISH' => 'QC Akhir',
                                                                default => ucfirst($rev->origin_status ?? '-')
                                                            };
                                                        @endphp
                                                        {{ $asalLabel }}
                                                    </td>
                                                    <td class="py-1 pr-3">
                                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase
                                                            {{ $rev->qc_stage === 'PRODUKSI' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' }}">
                                                            {{ $rev->qc_stage }}
                                                        </span>
                                                    </td>
                                                    <td class="py-1 pr-3 font-semibold text-gray-500 dark:text-gray-400">
                                                        {{ $rev->created_at ? $rev->created_at->translatedFormat('d M Y') : '-' }}
                                                    </td>
                                                    <td class="py-1 text-right font-black text-rose-600 dark:text-rose-400">
                                                        {{ isset($rev->loss_amount) && $rev->loss_amount > 0 ? 'Rp ' . number_format($rev->loss_amount, 0, ',', '.') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($revisionCount >= 3)
                                    <div class="mt-2.5 pt-2 border-t border-red-200 dark:border-red-800 text-[10px] font-bold text-red-700 dark:text-red-400">
                                        SPK ini sudah direvisi 3x atau lebih dan telah/akan masuk Rak Follow Up CX.
                                    </div>
                                @elseif($revisionCount == 2)
                                    <div class="mt-2.5 pt-2 border-t border-orange-200 dark:border-orange-800 text-[10px] font-bold text-orange-700 dark:text-orange-400">
                                        Perhatian: SPK ini sudah direvisi 2x. Jika direvisi 1x lagi, akan otomatis masuk Rak Follow Up CX.
                                    </div>
                                @endif
                            </div>
                        @endif
                       {{-- Banner Rangkuman Resolusi CX --}}
                       @if($resolvedIssue)
                           <div class="p-4 bg-emerald-50/90 dark:bg-emerald-950/40 rounded-xl border-2 border-emerald-300 dark:border-emerald-800 shadow-sm">
                               <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                   <div class="flex items-center gap-2">
                                       <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[10px] font-black uppercase tracking-wider flex items-center gap-1">
                                           <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                           CX Follow Up Selesai (Resolved)
                                       </span>
                                       <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">
                                           Kategori: {{ $resolvedIssue->category }}
                                       </span>
                                   </div>
                                   @if($resolvedIssue->resolved_at)
                                       <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-100/80 dark:bg-emerald-900/60 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                                           Diselesaikan: {{ $resolvedIssue->resolved_at->translatedFormat('d M Y H:i') }} {{ $resolvedIssue->resolver ? '• ' . $resolvedIssue->resolver->name : '' }}
                                       </span>
                                   @endif
                               </div>

                               <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2 text-xs">
                                   @if($resolvedIssue->kendala_1 || $resolvedIssue->kendala_2 || $resolvedIssue->kendala || $resolvedIssue->description)
                                       <div class="bg-white/90 dark:bg-gray-800/90 p-3 rounded-lg border border-emerald-200 dark:border-emerald-900/60">
                                           <span class="text-[9px] font-black text-rose-600 uppercase tracking-widest block mb-1">⚠️ Kendala Yang Dilaporkan:</span>
                                           <p class="font-bold text-gray-800 dark:text-gray-200 text-xs leading-relaxed">
                                               {{ $resolvedIssue->kendala_1 ?: ($resolvedIssue->kendala ?: ($resolvedIssue->description ?: 'Kendala Teknis/Material')) }}
                                               @if($resolvedIssue->kendala_2)
                                                   <span class="block text-[11px] text-gray-600 dark:text-gray-400 font-normal mt-0.5">• {{ $resolvedIssue->kendala_2 }}</span>
                                               @endif
                                           </p>
                                       </div>
                                   @endif

                                   @if($resolvedIssue->resolution_notes || $resolvedIssue->opsi_solusi_1 || $resolvedIssue->resolution)
                                       <div class="bg-white/90 dark:bg-gray-800/90 p-3 rounded-lg border border-emerald-200 dark:border-emerald-900/60">
                                           <span class="text-[9px] font-black text-emerald-700 uppercase tracking-widest block mb-1">✅ Keputusan / Solusi CS:</span>
                                           <p class="font-bold text-gray-800 dark:text-gray-200 text-xs leading-relaxed">
                                               {{ $resolvedIssue->resolution_notes ?: ($resolvedIssue->opsi_solusi_1 ?: $resolvedIssue->resolution) }}
                                           </p>
                                       </div>
                                   @endif
                               </div>

                               @if($resolvedIssue->estimasi_tambahan || $resolvedIssue->rec_service_1 || $resolvedIssue->rec_service_2)
                                   <div class="mt-2.5 flex flex-wrap gap-2 text-[10px] font-bold">
                                       @if($resolvedIssue->estimasi_tambahan)
                                           <span class="px-2.5 py-1 rounded bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                               ⏱️ Tambahan Waktu: {{ $resolvedIssue->estimasi_tambahan }}
                                           </span>
                                       @endif
                                       @if($resolvedIssue->rec_service_1)
                                           <span class="px-2.5 py-1 rounded bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-800">
                                               🛠️ Jasa Tambahan 1: {{ $resolvedIssue->rec_service_1 }}
                                           </span>
                                       @endif
                                       @if($resolvedIssue->rec_service_2)
                                           <span class="px-2.5 py-1 rounded bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-800">
                                               🛠️ Jasa Tambahan 2: {{ $resolvedIssue->rec_service_2 }}
                                           </span>
                                       @endif
                                   </div>
                               @endif
                           </div>
                       @endif

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

                        {{-- Informasi SLA Stasiun Kerja --}}
                        @php
                            $activeStatusName = 'SORTIR';
                            if (strpos($type, 'prep_') !== false) {
                                $activeStatusName = 'PREPARATION';
                            } elseif (strpos($type, 'prod_') !== false) {
                                $activeStatusName = 'PRODUCTION';
                            } elseif (strpos($type, 'qc_') !== false) {
                                $activeStatusName = 'QC';
                            }
                            $entryTime = $order->getStatusChangedAt($activeStatusName);
                            $durationDays = (int) $entryTime->diffInDays(now());
                            $durationHours = (int) ($entryTime->diffInHours(now()) % 24);
                        @endphp
                        <div class="p-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm space-y-3">
                            <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-2">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="block font-bold text-gray-400 dark:text-gray-505 uppercase text-[9px] tracking-wider">Informasi SLA Stasiun ({{ $activeStatusName }})</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-semibold text-gray-750 dark:text-gray-300">
                                <div>
                                    <span class="block text-[8px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider mb-0.5">SPK Dibuat (Created At)</span>
                                    <span class="text-gray-900 dark:text-white font-bold">{{ $order->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider mb-0.5">Masuk Stasiun Ini</span>
                                    <span class="text-gray-900 dark:text-white font-bold">{{ $entryTime->format('d M Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider mb-0.5">Durasi di Stasiun</span>
                                    <span class="text-gray-900 dark:text-white font-bold">{{ $durationDays }} Hari {{ $durationHours }} Jam</span>
                                </div>
                                <div class="sm:col-span-3 border-t border-gray-100 dark:border-gray-700 pt-2 mt-1">
                                    <span class="text-gray-400 font-medium">Aturan Target SLA Stasiun (Dihitung sejak Masuk Stasiun):</span>
                                    <span class="text-gray-900 dark:text-white font-bold ml-1">
                                        @if($activeStatusName === 'SORTIR')
                                            Maksimal 3 Hari sejak masuk stasiun Sortir
                                        @elseif($activeStatusName === 'PREPARATION')
                                            Maksimal 2 Hari (Fast Track: 1 Hari sejak masuk stasiun Prep)
                                        @elseif($activeStatusName === 'PRODUCTION')
                                            Maksimal 4 Hari sejak masuk stasiun Produksi
                                        @elseif($activeStatusName === 'QC')
                                            Maksimal 2 Hari (Fast Track: 1 Hari sejak masuk stasiun QC)
                                        @endif
                                    </span>
                                </div>
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
                            @if(str_starts_with($type, 'prep_') && !$isReviewTab)
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col justify-between shadow-sm h-fit space-y-4">
                                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 mb-2">
                                        <h4 class="text-xs font-bold text-gray-400 dark:text-gray-555 uppercase tracking-wider">Kontrol Pengerjaan</h4>
                                        <button type="button" 
                                                @click="if(confirm('Selesaikan seluruh proses persiapan (Cuci, Sol, Upper) untuk SPK ini secara instan?')) { Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu sebentar.', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } }); $wire.completeAllPrep({{ $order->id }}); expanded = false; }"
                                                class="px-2 py-1 text-[9px] font-black uppercase tracking-wider bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-all shadow active:scale-95 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Selesaikan Semua
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        {{-- 1. WASHING CONTROLS (Always required) --}}
                                        <div class="p-3 bg-teal-50/40 dark:bg-teal-955/20 border border-teal-100 dark:border-teal-900/40 rounded-xl space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-teal-700 dark:text-teal-400 uppercase tracking-widest">🧼 WASHING (CUCI)</span>
                                                @if($order->prep_washing_completed_at)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[8px] font-black rounded uppercase">SELESAI</span>
                                                @elseif($order->prep_washing_started_at)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[8px] font-black rounded uppercase animate-pulse">PROSES</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 text-[8px] font-black rounded uppercase">PENDING</span>
                                                @endif
                                            </div>

                                            @if($order->prep_washing_completed_at)
                                                <div class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                    Dikerjakan: <span class="text-teal-700">{{ $order->prepWashingBy->name ?? '-' }}</span>
                                                </div>
                                            @else
                                                @if(!$order->prep_washing_by)
                                                    <div class="space-y-2">
                                                        <select id="tech-prep_washing-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 focus:ring-teal-500 focus:border-teal-500 font-semibold dark:bg-gray-700 dark:text-white">
                                                            <option value="">-- TEKNISI --</option>
                                                            @if($technicians instanceof \Illuminate\Support\Collection || is_array($technicians))
                                                                @foreach($technicians as $t)
                                                                    @if(is_object($t) && isset($t->id))
                                                                        @php
                                                                            $poolTag = in_array($t->station ?? '', ['SOLING', 'UPPER', 'TREATMENT']) ? '⚪ Abu' : '🟢 Hijau';
                                                                            $specTag = $t->specialization ? " - {$t->specialization}" : '';
                                                                        @endphp
                                                                        <option value="{{ $t->id }}">{{ $t->name }}{{ $specTag }} ({{ $poolTag }})</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <button type="button" @click="window.updateStation({{ $order->id }}, 'prep_washing', 'start', {{ $order->prep_washing_by ?? 'null' }});" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Mulai
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="space-y-2">
                                                        <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold">
                                                            Pekerja: <span class="text-teal-600 dark:text-teal-400">{{ $order->prepWashingBy->name ?? '-' }}</span>
                                                            @if($order->prep_washing_started_at)
                                                                <div class="text-[8px] text-gray-400 mt-0.5 font-normal">Mulai: {{ $order->prep_washing_started_at->format('H:i') }} WIB</div>
                                                            @endif
                                                        </div>
                                                        <button type="button" @click="finishType = 'prep_washing'; showFinishModal = true" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Selesaikan
                                                        </button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                        {{-- 2. SOL PREP CONTROLS (Only if needs_prep_sol) --}}
                                        @if($order->needs_prep_sol)
                                        <div class="p-3 bg-orange-50/40 dark:bg-orange-955/20 border border-orange-100 dark:border-orange-900/40 rounded-xl space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Sol Prep</span>
                                                @if($order->prep_sol_completed_at)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[8px] font-black rounded uppercase">SELESAI</span>
                                                @elseif($order->prep_sol_started_at)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[8px] font-black rounded uppercase animate-pulse">PROSES</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 text-[8px] font-black rounded uppercase">PENDING</span>
                                                @endif
                                            </div>

                                            @if($order->prep_sol_completed_at)
                                                <div class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                    Dikerjakan: <span class="text-orange-600">{{ $order->prepSolBy->name ?? '-' }}</span>
                                                </div>
                                            @else
                                                @if(!$order->prep_sol_by)
                                                    <div class="space-y-2">
                                                        <select id="tech-prep_sol-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 focus:ring-orange-500 focus:border-orange-500 font-semibold dark:bg-gray-700 dark:text-white">
                                                            <option value="">-- TEKNISI SOL --</option>
                                                            @foreach($technicians['sol'] ?? [] as $t)
                                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" @click="window.updateStation({{ $order->id }}, 'prep_sol', 'start', {{ $order->prep_sol_by ?? 'null' }});" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Mulai
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="space-y-2">
                                                        <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold">
                                                            Pekerja: <span class="text-orange-600 dark:text-orange-400">{{ $order->prepSolBy->name ?? '-' }}</span>
                                                            @if($order->prep_sol_started_at)
                                                                <div class="text-[8px] text-gray-400 mt-0.5 font-normal">Mulai: {{ $order->prep_sol_started_at->format('H:i') }} WIB</div>
                                                            @endif
                                                        </div>
                                                        <button type="button" @click="finishType = 'prep_sol'; showFinishModal = true" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Selesaikan
                                                        </button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        @endif

                                        {{-- 3. UPPER PREP CONTROLS (Only if needs_prep_upper) --}}
                                        @if($order->needs_prep_upper)
                                        <div class="p-3 bg-purple-50/40 dark:bg-purple-955/20 border border-purple-100 dark:border-purple-900/40 rounded-xl space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest">Upper Prep</span>
                                                @if($order->prep_upper_completed_at)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[8px] font-black rounded uppercase">SELESAI</span>
                                                @elseif($order->prep_upper_started_at)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[8px] font-black rounded uppercase animate-pulse">PROSES</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 text-[8px] font-black rounded uppercase">PENDING</span>
                                                @endif
                                            </div>

                                            @if($order->prep_upper_completed_at)
                                                <div class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                    Dikerjakan: <span class="text-purple-600">{{ $order->prepUpperBy->name ?? '-' }}</span>
                                                </div>
                                            @else
                                                @if(!$order->prep_upper_by)
                                                    <div class="space-y-2">
                                                        <select id="tech-prep_upper-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 focus:ring-purple-500 focus:border-purple-500 font-semibold dark:bg-gray-700 dark:text-white">
                                                            <option value="">-- TEKNISI UPPER --</option>
                                                            @foreach($technicians['upper'] ?? [] as $t)
                                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" @click="window.updateStation({{ $order->id }}, 'prep_upper', 'start', {{ $order->prep_upper_by ?? 'null' }});" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Mulai
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="space-y-2">
                                                        <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold">
                                                            Pekerja: <span class="text-purple-600 dark:text-purple-400">{{ $order->prepUpperBy->name ?? '-' }}</span>
                                                            @if($order->prep_upper_started_at)
                                                                <div class="text-[8px] text-gray-400 mt-0.5 font-normal">Mulai: {{ $order->prep_upper_started_at->format('H:i') }} WIB</div>
                                                            @endif
                                                        </div>
                                                        <button type="button" @click="finishType = 'prep_upper'; showFinishModal = true" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Selesaikan
                                                        </button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Finish Modal --}}
                                    <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;" x-transition>
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-5 w-80 border border-gray-200 dark:border-gray-700" @click.away="showFinishModal = false">
                                            <h3 class="font-bold text-gray-800 dark:text-white text-sm mb-2 uppercase tracking-widest">Konfirmasi Selesai</h3>
                                            <p class="text-xs text-gray-555 mb-3 font-bold">Masukkan waktu selesai aktual:</p>
                                            <input type="datetime-local" x-model="finishDate" class="w-full text-xs border-gray-300 dark:border-gray-650 rounded-lg mb-4 focus:ring-green-500 focus:border-green-500 dark:bg-gray-750 dark:text-white">
                                            <div class="flex justify-end gap-2">
                                                <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-bold">Batal</button>
                                                <button @click="window.updateStation({{ $order->id }}, finishType, 'finish', null, finishDate); showFinishModal = false; expanded = false;" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-xs font-bold">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @elseif($type === 'prod_reparasi')
                                @php
                                    $hasUpperDrawer = $order->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'upper') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'upper'));
                                    $hasSolDrawer = $order->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'sol') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'sol'));
                                    $hasJahitDrawer = $hasSolDrawer || $hasUpperDrawer || $order->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'jahit') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'jahit'));

                                    if (!$hasUpperDrawer && !$hasSolDrawer && !$hasJahitDrawer) {
                                        $hasUpperDrawer = true;
                                    }

                                    $isSolLockedDrawer = $hasUpperDrawer && !$order->prod_upper_completed_at && !$order->isStationUnneeded('prod_upper');
                                    $isJahitLockedDrawer = ($hasUpperDrawer && !$order->prod_upper_completed_at && !$order->isStationUnneeded('prod_upper')) || ($hasSolDrawer && !$order->prod_sol_completed_at && !$order->isStationUnneeded('prod_sol'));
                                @endphp
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col justify-between shadow-sm h-fit space-y-4">
                                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 mb-2">
                                        <h4 class="text-xs font-bold text-gray-400 dark:text-gray-555 uppercase tracking-wider">Kontrol Pengerjaan Produksi</h4>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        {{-- 1. UPPER CONTROLS --}}
                                        @if($hasUpperDrawer)
                                        <div class="p-3 bg-purple-50/40 dark:bg-purple-955/20 border border-purple-100 dark:border-purple-900/40 rounded-xl space-y-2 relative">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-purple-700 dark:text-purple-400 uppercase tracking-widest">REPARASI UPPER</span>
                                                @if($order->isStationUnneeded('prod_upper'))
                                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[8px] font-black rounded uppercase">TIDAK DIPERLUKAN</span>
                                                @elseif($order->prod_upper_completed_at)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[8px] font-black rounded uppercase">SELESAI</span>
                                                @elseif($order->prod_upper_started_at)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[8px] font-black rounded uppercase animate-pulse">PROSES</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 text-[8px] font-black rounded uppercase">PENDING</span>
                                                @endif
                                            </div>

                                            @if($order->isStationUnneeded('prod_upper'))
                                                <div class="text-[10px] font-bold text-slate-500 italic">
                                                    Status: <span class="text-slate-600 dark:text-slate-400">Tidak Diperlukan</span>
                                                </div>
                                            @elseif($order->prod_upper_completed_at)
                                                <div class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                    Dikerjakan: <span class="text-purple-750">{{ $order->prodUpperBy->name ?? '-' }}</span>
                                                </div>
                                            @else
                                                @if(!$order->prod_upper_by)
                                                    <div class="space-y-2">
                                                        <select id="tech-prod_upper-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 focus:ring-purple-500 focus:border-purple-500 font-semibold dark:bg-gray-700 dark:text-white">
                                                            <option value="">-- TEKNISI UPPER --</option>
                                                            @foreach($technicians['upper'] ?? [] as $t)
                                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" @click="window.updateStation({{ $order->id }}, 'prod_upper', 'start', {{ $order->prod_upper_by ?? 'null' }});" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Mulai Pengerjaan
                                                        </button>
                                                    </div>
                                                @elseif(!$order->prod_upper_started_at)
                                                    <div class="space-y-2">
                                                        <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold flex justify-between items-center">
                                                            <span>Pekerja: <span class="text-purple-600 dark:text-purple-400">{{ $order->prodUpperBy->name ?? '-' }}</span></span>
                                                            <span class="text-[9px] text-amber-600 font-normal italic">Belum Dimulai</span>
                                                        </div>
                                                        <button type="button" @click="window.updateStation({{ $order->id }}, 'prod_upper', 'start', {{ $order->prod_upper_by ?? 'null' }});" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Mulai Pengerjaan
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="space-y-2">
                                                        <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold">
                                                            Pekerja: <span class="text-purple-600 dark:text-purple-400">{{ $order->prodUpperBy->name ?? '-' }}</span>
                                                            <div class="text-[8px] text-gray-400 mt-0.5 font-normal">Mulai: {{ $order->prod_upper_started_at->format('H:i') }} WIB</div>
                                                        </div>
                                                        <button type="button" @click="finishType = 'prod_upper'; showFinishModal = true" 
                                                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                            Selesaikan
                                                        </button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        @endif

                                        {{-- 2. SOLING CONTROLS --}}
                                        @if($hasSolDrawer)
                                        <div class="p-3 bg-orange-50/40 dark:bg-orange-955/20 border border-orange-100 dark:border-orange-900/40 rounded-xl space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-orange-700 dark:text-orange-400 uppercase tracking-widest">🥾 SOLING (SOL)</span>
                                                @if($order->isStationUnneeded('prod_sol'))
                                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[8px] font-black rounded uppercase">TIDAK DIPERLUKAN</span>
                                                @elseif($order->prod_sol_completed_at)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[8px] font-black rounded uppercase">SELESAI</span>
                                                @elseif($order->prod_sol_started_at)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[8px] font-black rounded uppercase animate-pulse">PROSES</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 text-[8px] font-black rounded uppercase">PENDING</span>
                                                @endif
                                            </div>

                                            @if($isSolLockedDrawer)
                                                <div class="p-2 bg-yellow-50 dark:bg-yellow-950/20 rounded-lg text-[10px] text-yellow-800 dark:text-yellow-400 border border-yellow-250 font-bold">
                                                    Menunggu stasiun Upper selesai (Sequencing)
                                                </div>
                                            @else
                                                @if($order->isStationUnneeded('prod_sol'))
                                                    <div class="text-[10px] font-bold text-slate-500 italic">
                                                        Status: <span class="text-slate-600 dark:text-slate-400">Tidak Diperlukan</span>
                                                    </div>
                                                @elseif($order->prod_sol_completed_at)
                                                    <div class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                        Dikerjakan: <span class="text-orange-700">{{ $order->prodSolBy->name ?? '-' }}</span>
                                                    </div>
                                                @else
                                                    @if(!$order->prod_sol_by)
                                                        <div class="space-y-2">
                                                            <select id="tech-prod_sol-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 focus:ring-orange-500 focus:border-orange-500 font-semibold dark:bg-gray-700 dark:text-white">
                                                                <option value="">-- TEKNISI SOL --</option>
                                                                @foreach($technicians['sol'] ?? [] as $t)
                                                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" @click="window.updateStation({{ $order->id }}, 'prod_sol', 'start', {{ $order->prod_sol_by ?? 'null' }});" 
                                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                                Mulai Pengerjaan
                                                            </button>
                                                        </div>
                                                    @elseif(!$order->prod_sol_started_at)
                                                        <div class="space-y-2">
                                                            <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold flex justify-between items-center">
                                                                <span>Pekerja: <span class="text-orange-600 dark:text-orange-400">{{ $order->prodSolBy->name ?? '-' }}</span></span>
                                                                <span class="text-[9px] text-amber-600 font-normal italic">Belum Dimulai</span>
                                                            </div>
                                                            <button type="button" @click="window.updateStation({{ $order->id }}, 'prod_sol', 'start', {{ $order->prod_sol_by ?? 'null' }});" 
                                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                                Mulai Pengerjaan
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="space-y-2">
                                                            <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold">
                                                                Pekerja: <span class="text-orange-600 dark:text-orange-400">{{ $order->prodSolBy->name ?? '-' }}</span>
                                                                <div class="text-[8px] text-gray-400 mt-0.5 font-normal">Mulai: {{ $order->prod_sol_started_at->format('H:i') }} WIB</div>
                                                            </div>
                                                            <button type="button" @click="finishType = 'prod_sol'; showFinishModal = true" 
                                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                                Selesaikan
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        @endif

                                        {{-- 3. QC JAHIT CONTROLS --}}
                                        @if($hasJahitDrawer)
                                        <div class="p-3 bg-blue-50/40 dark:bg-blue-955/20 border border-blue-100 dark:border-blue-900/40 rounded-xl space-y-2 relative">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-blue-700 dark:text-blue-400 uppercase tracking-widest">QC JAHIT</span>
                                                @if($order->isStationUnneeded('qc_jahit'))
                                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[8px] font-black rounded uppercase">TIDAK DIPERLUKAN</span>
                                                @elseif($order->qc_jahit_completed_at)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[8px] font-black rounded uppercase">SELESAI</span>
                                                @elseif($order->qc_jahit_started_at)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[8px] font-black rounded uppercase animate-pulse">PROSES</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 text-[8px] font-black rounded uppercase">PENDING</span>
                                                @endif
                                            </div>

                                            @if($isJahitLockedDrawer)
                                                <div class="p-2 bg-yellow-50 dark:bg-yellow-950/20 rounded-lg text-[10px] text-yellow-800 dark:text-yellow-400 border border-yellow-250 font-bold">
                                                    Menunggu konstruksi Upper &amp; Soling selesai (Sequencing)
                                                </div>
                                            @else
                                                @if($order->isStationUnneeded('qc_jahit'))
                                                    <div class="text-[10px] font-bold text-slate-500 italic">
                                                        Status: <span class="text-slate-600 dark:text-slate-400">Tidak Diperlukan</span>
                                                    </div>
                                                @elseif($order->qc_jahit_completed_at)
                                                    <div class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                        Dikerjakan: <span class="text-blue-700">{{ $order->qcJahitBy->name ?? '-' }}</span>
                                                    </div>
                                                @else
                                                    @if(!$order->qc_jahit_by)
                                                        <div class="space-y-2">
                                                            <select id="tech-qc_jahit-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 focus:ring-blue-500 focus:border-blue-500 font-semibold dark:bg-gray-700 dark:text-white">
                                                                <option value="">-- TEKNISI QC JAHIT --</option>
                                                                @foreach($technicians['jahit'] ?? ($technicians['all'] ?? []) as $t)
                                                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" @click="window.updateStation({{ $order->id }}, 'qc_jahit', 'start', {{ $order->qc_jahit_by ?? 'null' }});" 
                                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                                Mulai Pengerjaan
                                                            </button>
                                                        </div>
                                                    @elseif(!$order->qc_jahit_started_at)
                                                        <div class="space-y-2">
                                                            <div class="text-[10px] text-slate-650 dark:text-slate-400 font-bold flex justify-between items-center">
                                                                <span>Pekerja: <span class="text-blue-600 dark:text-blue-400">{{ $order->qcJahitBy->name ?? '-' }}</span></span>
                                                                <span class="text-[9px] text-amber-600 font-normal italic">Belum Dimulai</span>
                                                            </div>
                                                            <button type="button" @click="window.updateStation({{ $order->id }}, 'qc_jahit', 'start', {{ $order->qc_jahit_by ?? 'null' }});" 
                                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                                Mulai Pengerjaan
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="space-y-2">
                                                            <div class="text-[10px] text-slate-600 dark:text-slate-400 font-bold">
                                                                Pekerja: <span class="text-blue-600 dark:text-blue-400">{{ $order->qcJahitBy->name ?? '-' }}</span>
                                                                <div class="text-[8px] text-gray-400 mt-0.5 font-normal">Mulai: {{ $order->qc_jahit_started_at->format('H:i') }} WIB</div>
                                                            </div>
                                                            <button type="button" @click="finishType = 'qc_jahit'; showFinishModal = true" 
                                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95">
                                                                Selesaikan
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Finish Modal --}}
                                    <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;" x-transition>
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-5 w-80 border border-gray-200 dark:border-gray-700" @click.away="showFinishModal = false">
                                            <h3 class="font-bold text-gray-800 dark:text-white text-sm mb-2 uppercase tracking-widest">Konfirmasi Selesai</h3>
                                            <p class="text-xs text-gray-555 mb-3 font-bold">Masukkan waktu selesai aktual:</p>
                                            <input type="datetime-local" x-model="finishDate" class="w-full text-xs border-gray-300 dark:border-gray-650 rounded-lg mb-4 focus:ring-green-500 focus:border-green-500 dark:bg-gray-750 dark:text-white">
                                            <div class="flex justify-end gap-2">
                                                <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-bold">Batal</button>
                                                <button @click="window.updateStation({{ $order->id }}, finishType, 'finish', null, finishDate); showFinishModal = false; expanded = false;" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-xs font-bold">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col justify-between shadow-sm h-fit">
                                    <h4 class="text-xs font-bold text-gray-400 dark:text-gray-555 uppercase tracking-wider mb-3">Kontrol Pengerjaan</h4>
                                    
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
                                                        @if($technicians instanceof \Illuminate\Support\Collection || is_array($technicians))
                                                            @foreach($technicians as $t)
                                                                @if(is_object($t) && isset($t->id))
                                                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
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
                                </div>
                            @endif
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

      {{-- Override Modal Row (inside same Alpine scope) --}}
      <tr x-show="showOverrideModal" x-cloak style="display:none;">
          <td colspan="{{ ($isReviewTab ?? false) ? 6 : 7 }}" class="p-0">
              <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                   @click.self="showOverrideModal = false; overrideReason = '';">
                  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                      <div class="flex items-center justify-between">
                          <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wide">Konfirmasi Override Teknisi</h3>
                          <button @click="showOverrideModal = false; overrideReason = '';"
                                  class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 transition-colors">
                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                          </button>
                      </div>
                      <div class="text-xs text-gray-600 dark:text-gray-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl p-3 space-y-1">
                          <p class="font-bold text-amber-800 dark:text-amber-300">Stasiun sudah berjalan. Perubahan teknisi memerlukan alasan.</p>
                          <div class="grid grid-cols-2 gap-2 mt-2">
                              <div>
                                  <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Teknisi Sekarang</span>
                                  <span class="font-bold text-gray-700 dark:text-gray-200" x-text="pendingCurrentTechName"></span>
                              </div>
                              <div>
                                  <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Diganti ke</span>
                                  <span class="font-bold text-blue-600 dark:text-blue-400" x-text="pendingNewTechName"></span>
                              </div>
                          </div>
                          <div class="mt-1">
                              <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Stasiun</span>
                              <span class="font-bold text-gray-700 dark:text-gray-200" x-text="pendingStationLabel"></span>
                          </div>
                      </div>
                      <div class="space-y-1.5">
                          <label class="block text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                              Alasan Override <span class="text-red-500">*</span>
                          </label>
                          <textarea x-model="overrideReason"
                                    placeholder="Tuliskan alasan pergantian teknisi di tengah pengerjaan..."
                                    rows="3"
                                    class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-750 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none placeholder:text-gray-400 dark:placeholder:text-gray-500"></textarea>
                          <p class="text-[10px] text-gray-400" x-text="overrideReason.trim().length + ' / minimal 5 karakter'"></p>
                      </div>
                      <div class="flex gap-3 pt-1">
                          <button @click="showOverrideModal = false; overrideReason = '';"
                                  class="flex-1 px-4 py-2.5 rounded-xl text-xs font-black bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-700 dark:text-gray-300 transition-all">
                              Batal
                          </button>
                          <button @click="submitOverride()"
                                  :disabled="overrideReason.trim().length < 5"
                                  :class="overrideReason.trim().length >= 5 ? 'bg-blue-600 hover:bg-blue-700 text-white cursor-pointer' : 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500'"
                                  class="flex-1 px-4 py-2.5 rounded-xl text-xs font-black transition-all">
                              Simpan Override
                          </button>
                      </div>
                  </div>
              </div>
          </td>
      </tr>
</tbody>
