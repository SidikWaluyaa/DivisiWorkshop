@props([
    'order', 
    'type', 
    'technicians',
    'titleAction' => 'Assign',
    'techByRelation', // e.g., 'prepWashingBy'
    'startedAtColumn', // e.g., 'prep_washing_started_at'
    'byColumn' // e.g., 'prep_washing_by'
])

<tbody x-data="{ 
         expanded: false,
         showPhotos: false, 
         showFinishModal: false, 
         finishDate: '{{ now()->format('Y-m-d\TH:i') }}',
         isHighlighted: false,
         init() {
             const urlParams = new URLSearchParams(window.location.search);
             const hl = urlParams.get('highlight');
             if (hl === '{{ $order->spk_number }}') {
                 this.isHighlighted = true;
                 this.expanded = true;
                 setTimeout(() => {
                     this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 }, 500);
                 setTimeout(() => {
                     this.isHighlighted = false;
                 }, 5000);
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
                 @if($showCheckbox ?? false)
                     <input type="checkbox" value="{{ $order->id }}" 
                            @change="$store.preparation.toggle('{{ $order->id }}')" 
                            :checked="$store.preparation.includes('{{ $order->id }}')"
                            class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500 dark:bg-gray-750 dark:border-gray-600">
                 @endif
                 <span class="font-bold text-gray-400">{{ $loopIteration ?? $order->id }}</span>
             </div>
         </td>

         {{-- Column 2: SPK Number --}}
         <td class="px-6 py-4 whitespace-nowrap">
             <div class="flex items-center gap-2">
                 <span class="font-mono font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-750 px-2.5 py-1 rounded-md text-xs border border-gray-200 dark:border-gray-600">
                     {{ $order->spk_number }}
                 </span>
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
             <div class="text-[10px] text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ $order->shoe_brand }} {{ $order->shoe_type }} - {{ $order->shoe_color }}</div>
         </td>

         {{-- Column 4: Services --}}
         <td class="px-6 py-4">
             <div class="flex flex-wrap gap-1">
                 @foreach($order->workOrderServices as $detail)
                     @php
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
                     <span class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded bg-{{ $serviceColor }}-50 text-{{ $serviceColor }}-700 dark:bg-{{ $serviceColor }}-950/20 dark:text-{{ $serviceColor }}-400 border border-{{ $serviceColor }}-200/50 dark:border-{{ $serviceColor }}-900/30">
                         {{ Str::limit($name, 20) }}
                     </span>
                 @endforeach
             </div>
         </td>

         {{-- Column 5: Technician --}}
         <td class="px-6 py-4 whitespace-nowrap">
             @php
                 $techId = $order->{$byColumn};
                 $techName = $order->{$techByRelation}->name ?? null;
                 $startedAt = $order->{$startedAtColumn};
                 
                 $colorClass = match($type) {
                     'washing' => 'teal',
                     'sol' => 'orange',
                     'upper' => 'purple',
                     default => 'gray'
                 };
             @endphp
             @if($techName)
                 <div class="flex items-center gap-1.5">
                     <div class="w-5 h-5 rounded-full bg-{{ $colorClass }}-100 dark:bg-{{ $colorClass }}-950/40 flex items-center justify-center text-[10px] text-{{ $colorClass }}-700 dark:text-{{ $colorClass }}-400 font-bold">
                         {{ substr($techName, 0, 1) }}
                     </div>
                     <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $techName }}</span>
                 </div>
             @else
                 <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ditugaskan</span>
             @endif
         </td>

         {{-- Column 6: Duration / Started At --}}
         <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
             @if($startedAt)
                 <div class="flex flex-col">
                     <span class="font-bold text-[9px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Durasi</span>
                     <span class="font-mono font-black text-gray-700 dark:text-gray-300" data-started-at="{{ $startedAt->toIso8601String() }}">
                         Calculating...
                     </span>
                 </div>
             @else
                 <span class="text-gray-400 dark:text-gray-600 italic">Belum berjalan</span>
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
                      <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Detail Informasi & Catatan</h4>
                      
                      {{-- Technician notes --}}
                      @if($order->technician_notes)
                          <div class="p-3.5 bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-500 rounded-r text-xs text-amber-900 dark:text-amber-300 font-medium">
                              <span class="block font-bold text-amber-600 uppercase text-[10px] tracking-wide mb-1">⚠️ Instruksi Teknisi:</span>
                              {{ $order->technician_notes }}
                          </div>
                      @endif

                      {{-- CS notes --}}
                      @if($order->notes)
                          <div class="p-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs text-gray-700 dark:text-gray-300">
                              <span class="block font-bold text-gray-400 dark:text-gray-505 uppercase text-[9px] tracking-wide mb-1">Catatan CS:</span>
                              "{{ $order->notes }}"
                          </div>
                      @endif

                      {{-- CX follow up --}}
                      @if($resolvedIssue = $order->cxIssues->where('status', 'RESOLVED')->last())
                          <div class="p-3.5 bg-purple-50 dark:bg-purple-950/20 border-l-4 border-purple-500 rounded-r text-xs text-purple-900 dark:text-purple-300 font-medium">
                              <span class="block font-bold text-purple-600 uppercase text-[10px] tracking-wide mb-1"> Riwayat Follow Up CX:</span>
                              <div class="italic">"{{ $resolvedIssue->resolution_notes ?? $resolvedIssue->description ?? '-' }}"</div>
                              <div class="mt-2 text-[9px] text-purple-500 flex items-center gap-1">
                                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                  </svg>
                                  Selesai oleh {{ $resolvedIssue->resolver->name ?? 'System' }} • {{ $resolvedIssue->updated_at->format('d M H:i') }}
                              </div>
                          </div>
                      @endif
                      
                      {{-- Photo view/upload triggers --}}
                      <div class="pt-2 flex items-center gap-2">
                          <button @click="showPhotos = !showPhotos" 
                                  :class="showPhotos ? 'bg-teal-100 text-teal-700 border-teal-300 dark:bg-teal-950 dark:text-teal-400 dark:border-teal-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700'" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all shadow-sm">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                              </svg>
                              Kelola Foto
                          </button>

                          <button type="button" @click.stop="$dispatch('open-report-modal', {{ $order->id }})" 
                                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border-2 border-amber-200 hover:bg-amber-100 dark:bg-amber-955/20 dark:text-amber-400 dark:border-amber-900/30 transition-all shadow-sm">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                              </svg>
                              Lapor Kendala
                          </button>
                      </div>
                      
                      {{-- Slide-down photos gallery --}}
                      <div x-show="showPhotos" class="pt-4 border-t border-gray-200 dark:border-gray-700" style="display: none;" x-transition>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                              <div>
                                  <span class="text-[10px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider block mb-1.5">Before (Awal)</span>
                                  <x-photo-uploader :order="$order" :step="strtoupper('PREP_' . $type . '_BEFORE')" />
                              </div>
                              <div>
                                  <span class="text-[10px] font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider block mb-1.5">After (Akhir)</span>
                                  <x-photo-uploader :order="$order" :step="strtoupper('PREP_' . $type . '_AFTER')" />
                              </div>
                          </div>
                      </div>
                  </div>

                  {{-- Controls Col --}}
                  <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col justify-between shadow-sm h-fit">
                      <h4 class="text-xs font-bold text-gray-400 dark:text-gray-505 uppercase tracking-wider mb-3">Kontrol Pengerjaan</h4>
                      
                      @if(!$techId)
                          <div class="space-y-3">
                              <div>
                                  <label class="block text-[10px] font-bold text-gray-455 dark:text-gray-500 uppercase mb-1">Pilih Teknisi</label>
                                  <select id="tech-{{ $type }}-{{ $order->id }}" class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-teal-500 focus:border-teal-500 font-medium dark:bg-gray-700 dark:text-white">
                                      <option value="">-- Pilih Teknisi --</option>
                                      @foreach($technicians as $t)
                                          <option value="{{ $t->id }}">{{ $t->name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                              <button type="button" @click="window.updateStation({{ $order->id }}, '{{ $type }}', 'start')" 
                                      class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-lg text-xs font-bold uppercase transition-all shadow-md shadow-teal-500/10">
                                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                  </svg>
                                  Assign & Mulai
                              </button>
                          </div>
                      @else
                          <div class="space-y-3">
                              <div class="bg-{{ $colorClass }}-50/50 dark:bg-{{ $colorClass }}-950/20 border border-{{ $colorClass }}-200/50 dark:border-{{ $colorClass }}-900/30 rounded-lg p-2.5">
                                  <div class="text-[9px] text-{{ $colorClass }}-600 dark:text-{{ $colorClass }}-400 font-bold uppercase tracking-wider mb-1">Pekerja Aktif</div>
                                  <div class="font-bold text-xs text-gray-800 dark:text-white">{{ $techName }}</div>
                                  @if($startedAt)
                                      <div class="text-[9px] text-gray-400 dark:text-gray-505 mt-0.5">Mulai: {{ $startedAt->format('H:i') }} WIB</div>
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
                              <div x-show="showFinishModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;" x-transition>
                                  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-5 w-80 border border-gray-200 dark:border-gray-700" @click.away="showFinishModal = false">
                                      <h3 class="font-bold text-gray-800 dark:text-white text-sm mb-2">Konfirmasi Selesai</h3>
                                      <p class="text-xs text-gray-500 mb-3">Masukkan tanggal & jam selesai aktual:</p>
                                      <input type="datetime-local" x-model="finishDate" class="w-full text-xs border-gray-300 dark:border-gray-600 rounded-lg mb-4 focus:ring-green-500 focus:border-green-500 dark:bg-gray-750 dark:text-white">
                                      <div class="flex justify-end gap-2">
                                          <button @click="showFinishModal = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 dark:text-gray-350 rounded-lg text-xs font-bold">Batal</button>
                                          <button @click="window.updateStation({{ $order->id }}, '{{ $type }}', 'finish', finishDate)" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold">Simpan & Selesai</button>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endif
                  </div>
              </div>
          </td>
     </tr>
</tbody>
