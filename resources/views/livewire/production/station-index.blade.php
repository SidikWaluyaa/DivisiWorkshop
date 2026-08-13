<div>
    <x-slot name="header">
         <div class="flex flex-col md:flex-row justify-between items-center gap-4">
             <div class="flex items-center gap-4">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                
                <div class="flex flex-col">
                    <h2 class="font-bold text-xl leading-tight tracking-wide text-white">
                        {{ __('Stasiun Produksi') }}
                    </h2>
                    <div class="text-xs font-medium text-white/90">
                       Proses & Pelacakan (Livewire PWA)
                    </div>
                </div>
             </div>

             <div class="flex items-center gap-3">
                  {{-- Search Form --}}
                 <div class="relative">
                     <input type="text" 
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari SPK / Customer..." 
                            class="pl-9 pr-4 py-1.5 text-sm !text-gray-900 !bg-white border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 shadow-sm w-48 transition-all focus:w-64">
                     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                         <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                         </svg>
                     </div>
                 </div>

                  <div class="px-3 py-1 bg-white/10 text-white rounded-full text-xs font-bold border border-white/20">
                     {{ $orders->total() }} Order Aktif
                 </div>
              </div>
         </div>
     </x-slot>

     <div class="py-6 bg-gray-50 min-h-screen">
         <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             
             {{-- Stats Overview --}}
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                 {{-- Reparasi Stat --}}
                 <div wire:click="setTab('reparasi')"
                      class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] hover:shadow-xl {{ $activeTab === 'reparasi' ? 'ring-4 ring-indigo-400 ring-opacity-50 shadow-md' : 'opacity-85' }}">
                     <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-600 opacity-95 group-hover:opacity-100 transition-opacity"></div>
                     <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                     <div class="relative z-10 flex items-center justify-between">
                         <div>
                             <div class="flex items-center gap-3 mb-2">
                                 <div class="p-2.5 bg-white/25 rounded-xl backdrop-blur-md">
                                     <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                 </div>
                                 <h3 class="text-white font-black text-lg">Antrean Kerja Reparasi</h3>
                             </div>
                             <p class="text-white/80 text-xs font-semibold">Tugas pengerjaan fisik di stasiun Sol, Upper, dan Treatment</p>
                         </div>
                         <div class="text-right">
                             <span class="text-5xl font-black text-white block">{{ $this->counts['reparasi'] }}</span>
                             <span class="text-white/90 text-[10px] font-black uppercase tracking-wider">Antrian</span>
                         </div>
                     </div>
                 </div>

                 {{-- Review Stat --}}
                 <div wire:click="setTab('review')"
                      class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] hover:shadow-xl {{ $activeTab === 'review' ? 'ring-4 ring-orange-400 ring-opacity-50 shadow-md' : 'opacity-85' }}">
                     <div class="absolute inset-0 bg-gradient-to-br from-slate-700 via-slate-800 to-gray-900 opacity-95 group-hover:opacity-100 transition-opacity"></div>
                     <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                     <div class="relative z-10 flex items-center justify-between">
                         <div>
                             <div class="flex items-center gap-3 mb-2">
                                 <div class="p-2.5 bg-white/25 rounded-xl backdrop-blur-md">
                                     <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                 </div>
                                 <h3 class="text-white font-black text-lg">Siap Approval Admin</h3>
                             </div>
                             <p class="text-white/80 text-xs font-semibold">Seluruh pengerjaan selesai, menunggu ACC untuk diteruskan ke QC</p>
                         </div>
                         <div class="text-right">
                             <span class="text-5xl font-black text-white block">{{ $this->counts['review'] }}</span>
                             <span class="text-white/90 text-[10px] font-black uppercase tracking-wider">SPK Siap ACC</span>
                         </div>
                     </div>
                 </div>
             </div>

             {{-- Filter Section --}}
             <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
                 <div class="flex flex-col xl:flex-row items-center gap-4">
                     {{-- Search --}}
                     <div class="relative flex-1 w-full">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                             <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                         </div>
                         <input type="text" 
                                wire:model.live.debounce.300ms="search"
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50 font-medium transition-all" 
                                placeholder="Cari SPK, Customer, atau Brand...">
                     </div>

                     {{-- Status Filter (Hanya Sedang Berjalan) --}}
                     @if($activeTab !== 'review')
                     <div class="w-full xl:w-auto">
                         <label class="inline-flex items-center gap-2 px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50/50 hover:bg-gray-100 cursor-pointer text-xs font-bold uppercase tracking-wider text-gray-700 select-none transition-all w-full xl:w-auto justify-center">
                             <input type="checkbox" wire:model.live="onlyInProgress" class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500 cursor-pointer">
                             <span>🏃 Sedang Berjalan</span>
                         </label>
                     </div>
                     @endif

                     {{-- Priority Filter --}}
                     <div class="w-full xl:w-48">
                         <select wire:model.live="priority" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                             <option value="all">⚡ Semua Prioritas</option>
                             <option value="urgent">🔴 PRIORITAS / URGENT</option>
                             <option value="regular">⚪ REGULER</option>
                         </select>
                     </div>

                     {{-- Technician Filter --}}
                     <div class="w-full xl:w-56">
                         <select wire:model.live="technicianFilter" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                             <option value="all">👤 Semua Petugas</option>
                             @foreach($this->techs['all'] ?? [] as $tech)
                                 <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                             @endforeach
                         </select>
                     </div>

                     {{-- Sort --}}
                     <div class="w-full xl:w-40">
                         <select wire:model.live="sort" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                             <option value="asc">📅 Terlama</option>
                             <option value="desc">🆕 Terbaru</option>
                         </select>
                     </div>

                     {{-- Auto-Assign Button --}}
                     <button wire:click="autoAssignUnassignedTechnicians"
                             wire:loading.attr="disabled"
                             class="px-3.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all shadow-xs flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer w-full xl:w-auto whitespace-nowrap"
                             title="Tugaskan teknisi terbaik secara otomatis untuk SPK yang belum terisi teknisi">
                         Auto-Assign Teknisi
                     </button>

                     {{-- Reset Button --}}
                     <button wire:click="$set('search', ''); $set('priority', 'all'); $set('technicianFilter', 'all'); $set('sort', 'asc'); $set('onlyInProgress', false);"
                             class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-505 rounded-xl transition-all active:scale-95 w-full xl:w-auto flex justify-center"
                             title="Reset Filter">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                     </button>
                 </div>
             </div>

             {{-- Content Area --}}
             <div class="space-y-6" wire:loading.class="opacity-50 transition-opacity">
                 @if($activeTab !== 'review')
                 <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                     <div class="p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-indigo-250 border-b flex flex-wrap justify-between items-center gap-3">
                          <div class="flex flex-wrap items-center gap-4">
                              <h3 class="font-bold text-indigo-900 flex items-center gap-2 text-sm">
                                  <span class="w-2 h-2 rounded-full bg-indigo-650 animate-pulse"></span> 
                                  Daftar Antrean Kerja Reparasi
                              </h3>

                              {{-- Substate Pills Filter --}}
                              <div class="flex items-center bg-white/80 p-1 rounded-xl border border-indigo-200 shadow-xs">
                                  <button wire:click="setSubstate('all')" 
                                          class="px-3 py-1 text-xs font-bold rounded-lg transition-all {{ $substate === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-gray-600 hover:text-indigo-800 hover:bg-indigo-50' }}">
                                      Semua ({{ $this->counts['reparasi'] ?? 0 }})
                                  </button>
                                  <button wire:click="setSubstate('in_progress')" 
                                          class="px-3 py-1 text-xs font-bold rounded-lg transition-all {{ $substate === 'in_progress' ? 'bg-blue-600 text-white shadow-xs' : 'text-blue-700 hover:bg-blue-50' }}">
                                      Sedang Dikerjakan ({{ $this->counts['in_progress'] ?? 0 }})
                                  </button>
                                  <button wire:click="setSubstate('queued')" 
                                          class="px-3 py-1 text-xs font-bold rounded-lg transition-all {{ $substate === 'queued' ? 'bg-amber-600 text-white shadow-xs' : 'text-amber-700 hover:bg-amber-50' }}">
                                      Dalam Antrean ({{ $this->counts['queued'] ?? 0 }})
                                  </button>
                              </div>
                          </div>

                          <div class="flex items-center gap-2">
                               <input type="checkbox" wire:model.live="selectAll" id="select-all-top" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 cursor-pointer">
                               <label for="select-all-top" class="text-xs font-bold text-gray-600 cursor-pointer">Pilih Semua</label>
                          </div>
                     </div>
                     <div class="p-4 bg-gray-50/50 relative min-h-[400px]">
                         {{-- Professional Loading Overlay --}}
                         <div wire:loading wire:target="setTab, search, priority, technicianFilter, sort, selectedItems, selectAll, onlyInProgress" 
                              class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-30 flex items-center justify-center rounded-xl transition-all duration-300">
                             <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                                 <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                 <div class="text-[10px] font-black text-indigo-700 mt-4 tracking-widest uppercase">Sinkronisasi Data Produksi...</div>
                             </div>
                         </div>
                         
                         <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                             <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                                 <thead class="bg-gray-50 dark:bg-gray-700">
                                     <tr>
                                         <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">No</th>
                                         <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">SPK</th>
                                         <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pelanggan & Sepatu</th>
                                         <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prioritas</th>
                                         <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progres & Teknisi</th>
                                         <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durasi / SLA</th>
                                         <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">Detail</th>
                                     </tr>
                                 </thead>
                                 @forelse($orders as $order)
                                      <x-station-card 
                                          wire:key="card-reparasi-{{ $order->id }}"
                                          :order="$order" 
                                          :type="'prod_reparasi'" 
                                          :technicians="$this->techs"
                                          titleAction="Assign"
                                          showCheckbox="true"
                                          :loopIteration="$loop->iteration"
                                      />
                                 @empty
                                     <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                                         <tr>
                                             <td colspan="7" class="p-8 text-center text-gray-400 dark:text-gray-555 font-medium italic">✨ Tidak ada antrian pengerjaan saat ini.</td>
                                         </tr>
                                     </tbody>
                                 @endforelse
                             </table>
                         </div>
                     </div>
                     @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                         <div class="p-4 border-t border-gray-100">
                             {{ $orders->links() }}
                         </div>
                     @endif
                 </div>
                 @else
                 {{-- ADMIN REVIEW SECTION --}}
                 <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border-2 border-indigo-500 relative">
                      {{-- Professional Loading Overlay --}}
                      <div wire:loading wire:target="setTab, search, priority, technicianFilter, sort, selectedItems, selectAll, onlyInProgress" 
                           class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-30 flex items-center justify-center rounded-xl transition-all duration-300">
                          <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                              <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                              <div class="text-[10px] font-black text-indigo-700 mt-4 tracking-widest uppercase">Sinkronisasi Data Produksi...</div>
                          </div>
                      </div>
                     <div class="bg-gradient-to-r from-indigo-650 to-slate-900 p-4 text-white flex justify-between items-center">
                         <h3 class="text-lg font-bold flex items-center gap-2">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             Menunggu Pemeriksaan Admin (Produksi Selesai)
                         </h3>
                         <div class="flex items-center gap-3">
                             @if($orders->count() > 0)
                             <button wire:click="approveAll" 
                                     wire:confirm="Apakah Anda yakin ingin menyetujui seluruh {{ $orders->count() }} antrean di stasiun ini?" 
                                     class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-2 shadow-lg transition-all active:scale-95 border border-green-500">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                 Approve Semua ({{ $orders->count() }})
                             </button>
                             @endif
                             <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold">{{ $orders->total() }} Order</span>
                         </div>
                     </div>
                     <div class="overflow-x-auto">
                         <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                             <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs font-bold">
                                 <tr>
                                     <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                                         <div class="flex items-center gap-2">
                                             <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-indigo-650 rounded focus:ring-indigo-500 cursor-pointer">
                                             <span>No</span>
                                         </div>
                                     </th>
                                     <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">SPK</th>
                                     <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pelanggan & Sepatu</th>
                                     <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prioritas</th>
                                     <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Pengerjaan</th>
                                     <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">Aksi</th>
                                 </tr>
                             </thead>
                             @forelse($orders as $order)
                                 <x-station-card 
                                     wire:key="order-{{ $order->id }}-review"
                                     :order="$order" 
                                     :type="'prod_review'" 
                                     :technicians="$this->techs"
                                     :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                     showCheckbox="true"
                                     :isReviewTab="true"
                                 />
                             @empty
                                 <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                                     <tr>
                                         <td colspan="7" class="p-12 text-center text-gray-400 dark:text-gray-505 italic">
                                             <span class="text-4xl block mb-2">✨</span>
                                             <p>Tidak ada antrian siap approval saat ini.</p>
                                         </td>
                                     </tr>
                                 </tbody>
                             @endforelse
                         </table>
                     </div>
                     @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                         <div class="p-4 border-t border-gray-100">
                             {{ $orders->links() }}
                         </div>
                     @endif
                 </div>
                 @endif
             </div>
         </div>

         {{-- FLOATING BULK ACTION BAR --}}
         @if(count($selectedItems) > 0)
         <div class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4" 
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="translate-y-full opacity-0"
              x-transition:enter-end="translate-y-0 opacity-100">
             <div class="bg-white/80 backdrop-blur-md border border-white/40 shadow-2xl rounded-2xl p-4 w-full max-w-4xl flex items-center justify-between gap-4">
                 <div class="flex items-center gap-4">
                     <span class="bg-gray-900 text-white px-3 py-1 rounded-md font-bold text-sm">{{ count($selectedItems) }} Terpilih</span>
                     <button wire:click="$set('selectedItems', [])" class="text-xs font-bold text-red-500">Batal</button>
                 </div>
                 <div class="flex items-center gap-2">
                     @if($activeTab === 'review')
                         <button wire:click="bulkAction('approve')" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-colors shadow-md active:scale-95">Approve &amp; Kirim ke QC</button>
                     @else
                         <button wire:click="bulkAction('finish_active')" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-md active:scale-95 flex items-center gap-1.5">
                             <span>⚡</span> Selesaikan Stasiun Aktif
                         </button>
                     @endif
                 </div>
             </div>
         </div>
         @endif

     </div>

     <x-revision-modal currentStage="PRODUCTION" />
     <x-report-modal />

     <script>
         document.addEventListener('livewire:init', () => {
             window.updateStation = (id, type, action, techId = null, finishedAt = null) => {
                 // If action is start and techId isn't provided, try to find it from the select
                 if (action === 'start' && !techId) {
                     const select = document.getElementById(`tech-${type}-${id}`);
                     techId = select ? select.value : null;
                     if (!techId) {
                         Swal.fire({
                             icon: 'error',
                             title: 'Pilih teknisi terlebih dahulu.',
                             showConfirmButton: true,
                             confirmButtonColor: '#EF4444',
                             confirmButtonText: 'Tutup',
                             toast: false,
                             position: 'center'
                         });
                         return;
                     }
                 }
                 Swal.fire({
                     title: 'Memproses...',
                     text: 'Mohon tunggu sebentar.',
                     allowOutsideClick: false,
                     didOpen: () => {
                         Swal.showLoading();
                     }
                 });
                 @this.updateStation(id, type, action, techId, finishedAt);
             };
         });
     </script>
</div>
