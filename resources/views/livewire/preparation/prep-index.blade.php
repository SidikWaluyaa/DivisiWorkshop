<div class="py-6 bg-gray-50" x-data="{ 
    activeTab: @entangle('activeTab'),
    selectedItems: @entangle('selectedItems'),
    selectAll: @entangle('selectAll'),
    toggleManifestSelection(event, orderIds) {
        if (event.target.checked) {
            orderIds.forEach(id => {
                if (!this.selectedItems.includes(String(id))) {
                    this.selectedItems.push(String(id));
                }
            });
        } else {
            this.selectedItems = this.selectedItems.filter(id => !orderIds.map(String).includes(String(id)));
        }
    }
}">
    {{-- Header Banner --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-gradient-to-r from-[#22AF85] to-emerald-700 p-6 rounded-3xl shadow-lg text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl shadow-sm border border-white/30">
                    🧼
                </div>
                <div>
                    <h2 class="font-black text-xl leading-tight tracking-wide">
                        Stasiun Persiapan (Preparation)
                    </h2>
                    <p class="text-xs font-semibold opacity-90">
                        Pengelolaan proses cuci &amp; preparasi bahan baku per batch manifest
                    </p>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full md:w-80">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari SPK / Pelanggan..." 
                       class="w-full pl-11 pr-4 py-2.5 bg-white/10 border border-white/30 rounded-2xl text-xs font-bold text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:bg-white/20 transition-all outline-none">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-white/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    @push('head')
        @vite(['resources/js/preparation.js'])
    @endpush


        {{-- Consolidated Stats Tabs --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Antrean Prep Stat --}}
            <div wire:click="setTab('queue')"
                 class="group relative overflow-hidden rounded-3xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] {{ $activeTab === 'queue' ? 'ring-4 ring-teal-400 ring-opacity-50' : 'opacity-85 grayscale-[10%] hover:grayscale-0' }}">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-400 via-teal-500 to-teal-600"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md w-fit mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div class="text-sm font-black text-white/90 uppercase tracking-widest mb-1">📥 Antrean Prep</div>
                        <div class="text-xs text-white/80 font-bold uppercase tracking-wider">Belum dikerjakan</div>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-black text-white leading-none mb-1">{{ $this->counts['queue'] }}</div>
                        <div class="text-[10px] text-white/80 font-bold uppercase tracking-wider">Unit SPK</div>
                    </div>
                </div>
            </div>

            {{-- Sedang Dikerjakan Stat --}}
            <div wire:click="setTab('in_progress')"
                 class="group relative overflow-hidden rounded-3xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] {{ $activeTab === 'in_progress' ? 'ring-4 ring-amber-400 ring-opacity-50' : 'opacity-85 grayscale-[10%] hover:grayscale-0' }}">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md w-fit mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-sm font-black text-white/90 uppercase tracking-widest mb-1">🏃 Sedang Dikerjakan</div>
                        <div class="text-xs text-white/80 font-bold uppercase tracking-wider">Lead time berjalan</div>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-black text-white leading-none mb-1">{{ $this->counts['in_progress'] }}</div>
                        <div class="text-[10px] text-white/80 font-bold uppercase tracking-wider">Unit SPK</div>
                    </div>
                </div>
            </div>

            {{-- Review Admin Stat --}}
            <div wire:click="setTab('review')"
                 class="group relative overflow-hidden rounded-3xl p-6 cursor-pointer transition-all duration-300 hover:scale-[1.02] {{ $activeTab === 'review' ? 'ring-4 ring-blue-400 ring-opacity-50' : 'opacity-85 grayscale-[10%] hover:grayscale-0' }}">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-650"></div>
                <div class="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md w-fit mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-sm font-black text-white/90 uppercase tracking-widest mb-1">👮 Review Admin</div>
                        <div class="text-xs text-white/80 font-bold uppercase tracking-wider">Menunggu persetujuan</div>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-black text-white leading-none mb-1">{{ $this->counts['review'] }}</div>
                        <div class="text-[10px] text-white/80 font-bold uppercase tracking-wider">Unit SPK</div>
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
                <button wire:click="$set('search', ''); $set('priority', 'all'); $set('technicianFilter', 'all'); $set('sort', 'asc');"
                        class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-505 rounded-xl transition-all active:scale-95 w-full xl:w-auto flex justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        {{-- Content Table/List --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px]">
            @php
                $activeTabLabel = match($activeTab) {
                    'queue' => 'Antrean Preparation (Belum Dikerjakan)',
                    'in_progress' => 'Sedang Dikerjakan (Live Lead Time)',
                    'review' => 'Review Admin (Selesai Washing)',
                    default => ''
                };
                $activeTabEmoji = match($activeTab) {
                    'queue' => '📥',
                    'in_progress' => '🏃',
                    'review' => '👮',
                    default => ''
                };
                $activeTabColor = match($activeTab) {
                    'queue' => 'teal',
                    'in_progress' => 'amber',
                    'review' => 'blue',
                    default => 'gray'
                };
            @endphp
            
            <div class="p-4 border-b border-{{ $activeTabColor }}-200 bg-gradient-to-r from-{{ $activeTabColor }}-50 to-{{ $activeTabColor }}-100 flex justify-between items-center">
                <h3 class="font-bold text-{{ $activeTabColor }}-800 flex items-center gap-2">
                    <span>{{ $activeTabEmoji }} {{ $activeTabLabel }}</span>
                    <span class="px-2 py-0.5 bg-white rounded-full text-[10px] border border-{{ $activeTabColor }}-200">{{ $orders->total() }} items</span>
                </h3>
                @if($activeTab === 'review')
                    @if($orders->count() > 0)
                    <button wire:click="approveAll" 
                            wire:confirm="Apakah Anda yakin ingin menyetujui seluruh {{ $orders->total() }} antrean di stasiun ini?" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-green-100 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Approve Semua ke Sortir ({{ $orders->total() }})
                    </button>
                    @endif
                @else
                <div class="flex items-center gap-4">
                     <div class="flex items-center gap-2">
                          <input type="checkbox" id="select-all-top" wire:model.live="selectAll" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 transition-all cursor-pointer">
                          <label for="select-all-top" class="text-[10px] font-black text-{{ $activeTabColor }}-700 cursor-pointer uppercase">Pilih Semua</label>
                      </div>
                 </div>
                @endif
            </div>

            <div class="overflow-x-auto relative">
                {{-- Local Loading Overlay --}}
                <div wire:loading wire:target="setTab, search, priority, technicianFilter, sort, selectedItems, selectAll, onlyInProgress" 
                     class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-30 flex items-center justify-center rounded-xl transition-all duration-300">
                    <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                        <div class="w-12 h-12 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
                <table class="min-w-full w-full divide-y divide-gray-250 dark:divide-gray-700 text-left font-sans">
                    <thead class="bg-gray-100 dark:bg-gray-750">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Manifest</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Catatan &amp; Waktu Lead Time</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">Progress SPK</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-44">Aksi Batch</th>
                        </tr>
                    </thead>
                    @forelse($orders as $group)
                        @php
                            $groupId = (string)$group->id;
                            $isExpanded = in_array($groupId, $expandedManifests) || !empty($search);
                            
                            $totalGroupSPK = $group->work_orders->count();
                            $completedGroupSPK = $group->work_orders->filter(function($wo) {
                                return !is_null($wo->prep_washing_completed_at);
                            })->count();
                            
                            $progressPercent = $totalGroupSPK > 0 ? round(($completedGroupSPK / $totalGroupSPK) * 100) : 0;
                            $allGroupOrderIds = $group->work_orders->pluck('id')->map(fn($id) => (string)$id)->toArray();
                            $isGroupAllSelected = count($allGroupOrderIds) > 0 && count(array_intersect($selectedItems, $allGroupOrderIds)) === count($allGroupOrderIds);

                            // Lead Time calculations
                            $earliestStarted = $group->work_orders->pluck('prep_washing_started_at')->filter()->sort()->first();
                            $latestCompleted = $group->work_orders->pluck('prep_washing_completed_at')->filter()->sort()->last();
                        @endphp
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800" wire:key="group-tbody-{{ $groupId }}">
                            <!-- Row Manifest Utama -->
                            <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="$wire.toggleManifest('{{ $groupId }}')">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-500">
                                    {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div @click.stop class="flex items-center">
                                            <input type="checkbox" 
                                                   @if($isGroupAllSelected) checked @endif
                                                   @change="toggleManifestSelection($event, [{{ implode(',', $allGroupOrderIds) }}])"
                                                   class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 cursor-pointer">
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-black text-xs text-gray-800 dark:text-white uppercase tracking-wider">
                                                {{ $group->manifest_number }}
                                            </span>
                                            @if($group->created_at)
                                                <span class="text-[10px] text-gray-400 font-bold mt-0.5">
                                                    Masuk: {{ \Carbon\Carbon::parse($group->created_at)->translatedFormat('d M Y H:i') }} WIB
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col gap-1">
                                        <span class="italic text-[11px]">{{ $group->notes ?? 'Tidak ada catatan.' }}</span>
                                        @if($earliestStarted)
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-mono font-bold border border-amber-200">
                                                    ⏱️ Mulai: {{ \Carbon\Carbon::parse($earliestStarted)->format('H:i \W\I\B') }}
                                                </span>
                                                @if($activeTab === 'in_progress')
                                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold border border-emerald-200 animate-pulse">
                                                        ⏳ Berjalan: {{ \Carbon\Carbon::parse($earliestStarted)->locale('id')->diffForHumans(null, true) }}
                                                    </span>
                                                @elseif($activeTab === 'review' && $latestCompleted)
                                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-mono font-bold border border-blue-200">
                                                        ✅ Selesai dalam: {{ \Carbon\Carbon::parse($earliestStarted)->locale('id')->diffForHumans(\Carbon\Carbon::parse($latestCompleted), true) }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center justify-center w-full max-w-[200px] mx-auto">
                                        <div class="flex items-center justify-between w-full text-[10px] font-bold text-teal-700 dark:text-teal-400 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $completedGroupSPK }} / {{ $totalGroupSPK }} SPK ({{ $progressPercent }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-teal-600 h-full rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap flex items-center justify-end gap-2">
                                    @if($activeTab === 'queue')
                                        <button type="button" 
                                                wire:click.stop="autoAssignManifestPrep('{{ $groupId }}')" 
                                                title="Bagi SPK ke teknisi secara adil & merata (Balanced Round-Robin)"
                                                class="px-2.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[10px] shadow-sm transition-all flex items-center gap-1 active:scale-95">
                                            <span>🤖 Auto Assign</span>
                                        </button>
                                        <button type="button" 
                                                wire:click.stop="startManifestPrep('{{ $groupId }}')" 
                                                wire:confirm="Mulai pengerjaan cuci untuk seluruh SPK di manifest ini?"
                                                class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-[10px] shadow-sm transition-all flex items-center gap-1 active:scale-95">
                                            <span>▶️ Mulai Batch</span>
                                        </button>
                                    @elseif($activeTab === 'in_progress')
                                        <button type="button" 
                                                wire:click.stop="completeManifestPrep('{{ $groupId }}')" 
                                                wire:confirm="Selesaikan pengerjaan cuci untuk seluruh SPK di manifest ini?"
                                                class="px-3 py-1.5 rounded-xl bg-[#22AF85] hover:bg-emerald-600 text-white font-black text-[10px] shadow-sm transition-all flex items-center gap-1 active:scale-95">
                                            <span>✅ Selesaikan Batch</span>
                                        </button>
                                    @endif

                                    <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[10px] font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all border border-slate-200">
                                        <svg class="w-3.5 h-3.5 transform transition-transform duration-200 @if($isExpanded) rotate-180 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <span>{{ $isExpanded ? 'Tutup' : 'Lihat SPK' }}</span>
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Accordion Dropdown SPK -->
                            @if($isExpanded)
                            <tr>
                                <td colspan="5" class="bg-gray-50/70 dark:bg-gray-900/30 p-4 border-t border-b border-gray-100 dark:border-gray-800">
                                    <div class="rounded-xl border border-gray-200/80 dark:border-gray-750 bg-white dark:bg-gray-850 shadow-sm overflow-hidden p-2">
                                        <table class="min-w-full w-full divide-y divide-gray-200 text-left">
                                            <thead class="bg-gray-50/50">
                                                <tr>
                                                    @if($activeTab === 'review')
                                                        <th class="px-6 py-2.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider w-20">Checkbox</th>
                                                    @else
                                                        <th class="px-6 py-2.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider w-16">Pilih</th>
                                                    @endif
                                                    <th class="px-6 py-2.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">SPK</th>
                                                    <th class="px-6 py-2.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Pelanggan & Sepatu</th>
                                                    <th class="px-6 py-2.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Prioritas</th>
                                                    @if($activeTab === 'review')
                                                        <th class="px-6 py-2.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">Status Pengerjaan</th>
                                                        <th class="px-6 py-2.5 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider w-20">Aksi</th>
                                                    @else
                                                        <th class="px-6 py-2.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Progress Tugas Prep</th>
                                                        <th class="px-6 py-2.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Estimasi</th>
                                                        <th class="px-6 py-2.5 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider w-20">Detail</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            @php
                                                $groupIteration = 1;
                                            @endphp
                                            @foreach($group->work_orders as $order)
                                                <x-station-card 
                                                    wire:key="order-{{ $order->id }}-{{ $activeTab }}"
                                                    :order="$order" 
                                                    :type="'prep_'.$activeTab" 
                                                    :technicians="$this->techs"
                                                    :loopIteration="$groupIteration++"
                                                    showCheckbox="true"
                                                    :isReviewTab="$activeTab === 'review'"
                                                />
                                            @endforeach
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    @empty
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                            <tr>
                                <td colspan="5" class="p-12 text-center text-gray-400 dark:text-gray-550 italic">
                                    <span class="text-4xl block mb-2">✨</span>
                                    <p>Tidak ada antrian di stasiun ini.</p>
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        </div>

        {{-- Floating Bulk Action Bar Component --}}
        <x-prep-bulk-bar :activeTab="$activeTab" :techs="$this->techs" />

    </div>

    {{-- Revision & Report Modals --}}
    <x-revision-modal currentStage="PREPARATION" />
    <x-report-modal />

    {{-- Action Scripts --}}
    @script
    <script>
        // Global listener for report modal
        window.openReportModal = function(orderId) {
            window.dispatchEvent(new CustomEvent('open-report-modal', { detail: orderId }));
        };
        window.submitBulkAssign = function() {
            const washingId = document.getElementById('bulk-tech-washing')?.value || '';
            const solId = document.getElementById('bulk-tech-sol')?.value || '';
            const upperId = document.getElementById('bulk-tech-upper')?.value || '';

            if (!washingId && !solId && !upperId) {
                Swal.fire({ icon: 'warning', title: 'Pilih Teknisi', text: 'Silakan pilih minimal satu teknisi terlebih dahulu.' });
                return;
            }
            confirmBulkAction('assign', { washing: washingId, sol: solId, upper: upperId });
        };
        window.submitBulkFinish = function() {
            const washingId = document.getElementById('bulk-tech-washing')?.value || '';
            const solId = document.getElementById('bulk-tech-sol')?.value || '';
            const upperId = document.getElementById('bulk-tech-upper')?.value || '';

            if (!washingId && !solId && !upperId) {
                Swal.fire({ icon: 'warning', title: 'Pilih Teknisi', text: 'Silakan pilih minimal satu teknisi terlebih dahulu.' });
                return;
            }
            confirmBulkAction('finish', { washing: washingId, sol: solId, upper: upperId });
        };
        window.confirmBulkCompleteAll = function() {
            const count = $wire.selectedItems.length;
            if (count === 0) return;

            Swal.fire({
                title: `Selesaikan Semua Prep (${count} SPK)?`,
                text: 'Seluruh sub-tugas persiapan (Cuci, Sol, Upper) untuk seluruh SPK terpilih akan dianggap selesai dan dipindahkan ke Review Admin.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Selesaikan Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $wire.bulkCompleteAllPrep();
                }
            });
        };
        window.confirmBulkAction = (action, techs = {}) => {
            const count = $wire.selectedItems.length;
            if (count === 0) return;

            const title = action === 'approve' ? `Setujui ${count} Order?` : `Proses ${count} Order?`;
            const text = action === 'approve' 
                ? 'Semua order terpilih akan langsung dikirim ke Sortir.' 
                : 'Perubahan status pengerjaan akan diterapkan untuk seluruh SPK terpilih.';
            
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $wire.bulkAction(action, techs);
                }
            });
        }

        $wire.on('swal:toast', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                icon: data.icon || 'success',
                title: data.title || 'Berhasil!',
                text: data.text || '',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                toast: false,
                position: 'center',
                iconColor: (data.icon || 'success') === 'success' ? '#1B8A68' : undefined
            });
        });

        window.confirmApprovePrep = (id) => {
             Swal.fire({
                title: 'Setujui Preparation?',
                text: "Order akan dipindahkan ke antrian Sortir.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $wire.performApprove(id);
                }
            });
        };

        window.updateStation = (id, type, action, techId = null, finishedAt = null) => {
            if (action === 'start') {
                const select = document.getElementById(`tech-${type}-${id}`);
                techId = select ? select.value : null;
                if (!techId) {
                    Swal.fire({ icon: 'warning', title: 'Pilih Teknisi', text: 'Silakan pilih teknisi persiapan terlebih dahulu.' });
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
            $wire.updateStation(id, type, action, techId, finishedAt);
        };
    </script>
    @endscript
</div>
