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
                       Proses & Pelacakan (Livewire)
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
                    {{ $orders->count() }} Order Aktif
                </div>
             </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Premium Stats Overview with Glassmorphism --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                {{-- Sol Stat --}}
                <div wire:click="setTab('sol')"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl {{ $activeTab === 'sol' ? 'ring-4 ring-orange-400 ring-opacity-50' : '' }}">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            @if($activeTab === 'sol')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Reparasi Sol</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $this->counts['sol'] }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </div>

                {{-- Upper Stat --}}
                <div wire:click="setTab('upper')"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl {{ $activeTab === 'upper' ? 'ring-4 ring-purple-400 ring-opacity-50' : '' }}">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400 via-purple-500 to-purple-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            @if($activeTab === 'upper')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Reparasi Upper</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $this->counts['upper'] }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </div>

                {{-- Treatment Stat --}}
                <div wire:click="setTab('treatment')"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl {{ $activeTab === 'treatment' ? 'ring-4 ring-teal-400 ring-opacity-50' : '' }}">
                    <div class="absolute inset-0 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            </div>
                            @if($activeTab === 'treatment')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Repaint & Treatment</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $this->counts['treatment'] }}</span>
                            <span class="text-white/70 text-sm font-medium">antrian</span>
                        </div>
                    </div>
                </div>

                {{-- All Orders Stat --}}
                <div wire:click="setTab('review')"
                     class="group relative overflow-hidden rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl {{ $activeTab === 'review' ? 'ring-4 ring-gray-400 ring-opacity-50' : '' }}">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-600 via-gray-700 to-gray-800 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            @if($activeTab === 'review')
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-md rounded-full text-white text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <h3 class="text-white font-black text-lg mb-1">Siap Approval</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{ $this->counts['review'] }}</span>
                            <span class="text-white/70 text-sm font-medium">order</span>
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
                    @if($activeTab !== 'review')
                    <div class="w-full xl:w-56">
                        <select wire:model.live="technicianFilter" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                            <option value="all">👤 Semua Petugas</option>
                            @foreach($this->techs[$activeTab] ?? [] as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Sort --}}
                    <div class="w-full xl:w-40">
                        <select wire:model.live="sort" class="w-full py-2.5 pl-3 pr-10 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                            <option value="asc">📅 Terlama</option>
                            <option value="desc">🆕 Terbaru</option>
                        </select>
                    </div>

                    {{-- Reset Button --}}
                    <button wire:click="$set('search', ''); $set('priority', 'all'); $set('technicianFilter', 'all'); $set('sort', 'asc'); $set('onlyInProgress', false);"
                            class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-505 rounded-xl transition-all active:scale-95 w-full xl:w-auto flex justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Content Area --}}
            <div class="space-y-6" wire:loading.class="opacity-50 transition-opacity">
                @if($activeTab !== 'review')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 bg-gradient-to-r {{ $activeTab === 'sol' ? 'from-orange-50 to-orange-100 border-orange-200' : ($activeTab === 'upper' ? 'from-purple-50 to-purple-100 border-purple-200' : 'from-teal-50 to-teal-100 border-teal-200') }} border-b flex justify-between items-center">
                        <h3 class="font-bold {{ $activeTab === 'sol' ? 'text-orange-800' : ($activeTab === 'upper' ? 'text-purple-800' : 'text-teal-800') }} flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $activeTab === 'sol' ? 'bg-orange-500' : ($activeTab === 'upper' ? 'bg-purple-500' : 'bg-teal-500') }}"></span> 
                            Antrian {{ $activeTab === 'sol' ? 'Reparasi Sol' : ($activeTab === 'upper' ? 'Reparasi Upper' : 'Repaint & Treatment') }}
                        </h3>
                        <div class="flex items-center gap-2">
                             <input type="checkbox" wire:model.live="selectAll" id="select-all-top" class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500 cursor-pointer">
                             <label for="select-all-top" class="text-xs font-bold text-gray-600 cursor-pointer">Pilih Semua</label>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50/50 relative min-h-[400px]">
                        {{-- Professional Loading Overlay --}}
                        <div wire:loading wire:target="setTab, search, priority, technicianFilter, sort" 
                             class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-30 flex items-center justify-center rounded-xl transition-all duration-300">
                            <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                                <div class="w-12 h-12 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                                <div class="text-[10px] font-black text-teal-700 mt-4 tracking-widest uppercase">Sinkronisasi Data Produksi...</div>
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
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teknisi</th>
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durasi / SLA</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">Detail</th>
                                    </tr>
                                </thead>
                                @forelse($orders as $order)
                                     <x-station-card 
                                         wire:key="card-{{ $activeTab }}-{{ $order->id }}"
                                         :order="$order" 
                                         :type="$activeTab === 'sol' ? 'prod_sol' : ($activeTab === 'upper' ? 'prod_upper' : 'prod_cleaning')" 
                                         :technicians="$this->techs[$activeTab]"
                                         :techByRelation="$activeTab === 'sol' ? 'prodSolBy' : ($activeTab === 'upper' ? 'prodUpperBy' : 'prodCleaningBy')"
                                         :startedAtColumn="$activeTab === 'sol' ? 'prod_sol_started_at' : ($activeTab === 'upper' ? 'prod_upper_started_at' : 'prod_cleaning_started_at')"
                                         :byColumn="$activeTab === 'sol' ? 'prod_sol_by' : ($activeTab === 'upper' ? 'prod_upper_by' : 'prod_cleaning_by')"
                                         :color="$activeTab === 'sol' ? 'orange' : ($activeTab === 'upper' ? 'purple' : 'teal')"
                                         titleAction="Assign"
                                         showCheckbox="true"
                                         :loopIteration="$loop->iteration"
                                     />
                                @empty
                                    <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                                        <tr>
                                            <td colspan="7" class="p-8 text-center text-gray-400 dark:text-gray-500 font-medium italic">✨ Tidak ada antrian saat ini.</td>
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
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border-2 border-orange-400">
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4 text-white flex justify-between items-center">
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
                            <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold">{{ $orders->count() }} Order</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 cursor-pointer">
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
                                    wire:key="order-{{ $order->id }}-{{ $activeTab }}"
                                    :order="$order" 
                                    :type="'prod_'.$activeTab" 
                                    :technicians="$this->techs[$activeTab] ?? collect()"
                                    :loopIteration="($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration"
                                    showCheckbox="true"
                                    :isReviewTab="true"
                                />
                            @empty
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                                    <tr>
                                        <td colspan="7" class="p-12 text-center text-gray-400 dark:text-gray-505 italic">
                                            <span class="text-4xl block mb-2">✨</span>
                                            <p>Tidak ada antrian di stasiun ini.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            @endforelse
                        </table>
                    </div>
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
                    @if($activeTab !== 'review')
                    <select id="bulk-tech-select-live" class="bg-white border border-gray-200 text-xs rounded-lg px-3 py-2.5 font-bold">
                        <option value="">-- PILIH TEKNISI --</option>
                        @foreach($this->techs[$activeTab] ?? [] as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
                    </select>
                    <button onclick="window.bulkActionLive('assign')" class="bg-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-bold">Assign</button>
                    <button onclick="window.bulkActionLive('finish')" class="bg-emerald-500 text-white px-5 py-2.5 rounded-lg text-xs font-bold">Selesai</button>
                    @else
                    <button wire:click="bulkAction('approve')" class="bg-green-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold">Approve & QC</button>
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
                @this.updateStation(id, type, action, techId, finishedAt);
            };

            window.bulkActionLive = (action) => {
                let techId = null;
                if (action === 'assign') {
                    const select = document.getElementById('bulk-tech-select-live');
                    techId = select ? select.value : null;
                    if (!techId) {
                        Swal.fire('Info', 'Pilih teknisi terlebih dahulu', 'info');
                        return;
                    }
                }
                
                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Lanjutkan aksi ${action} untuk item terpilih?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0D9488',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.bulkAction(action, techId);
                    }
                });
            };
        });
    </script>
</div>
