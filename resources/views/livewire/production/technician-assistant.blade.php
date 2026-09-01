<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 font-sans">
    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-[#1a3b34] via-[#22AF85] to-[#1a3b34] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-emerald-400/20">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/10 rounded-full border border-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-[#FFC232] shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                    <span>WORKLOAD &amp; LIVE TIMER SYSTEM</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight leading-tight">
                    Asisten Data <span class="text-[#FFC232]">Teknisi Workshop</span>
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 font-medium max-w-xl leading-relaxed">
                    Pemantauan kinerja real-time per stasiun pengerjaan (Cuci, Soling, Upper, Treatment/Repaint, QC) &amp; Live Timer Stopwatch.
                </p>
            </div>

            @if(session()->has('success'))
                <div class="p-3.5 bg-slate-900/90 border border-emerald-400/40 backdrop-blur-md rounded-2xl text-white text-xs font-bold flex items-center gap-3 shadow-xl">
                    <div class="w-7 h-7 rounded-xl bg-[#22AF85] flex items-center justify-center text-slate-950 font-black shrink-0 text-xs">✓</div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Station Category Filter Bar & Personnel Selector Container --}}
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-100 space-y-6" x-data="{
        scrollLeft() { $refs.techScroll.scrollBy({ left: -260, behavior: 'smooth' }) },
        scrollRight() { $refs.techScroll.scrollBy({ left: 260, behavior: 'smooth' }) }
    }">
        {{-- Station Filter Tabs --}}
        <div class="flex flex-col space-y-3 border-b border-slate-100 pb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#22AF85]"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-slate-800">Filter Stasiun Pengerjaan Teknisi</span>
                </div>
                <span class="text-[11px] font-extrabold text-slate-400">Total: {{ $countAll }} Teknisi Aktif</span>
            </div>

            {{-- Station Category Pills --}}
            <div class="flex items-center gap-2.5 overflow-x-auto pb-1.5 scrollbar-none">
                <button wire:click="filterStationCategory('ALL')" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all whitespace-nowrap active:scale-95 flex items-center gap-2 border
                        {{ $stationCategory === 'ALL' ? 'bg-[#22AF85] text-white border-[#22AF85] shadow-md ring-2 ring-[#22AF85]/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span>🌟 Semua</span>
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-black {{ $stationCategory === 'ALL' ? 'bg-slate-950/30 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $countAll }}</span>
                </button>

                <button wire:click="filterStationCategory('PREPARATION')" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all whitespace-nowrap active:scale-95 flex items-center gap-2 border
                        {{ $stationCategory === 'PREPARATION' ? 'bg-cyan-600 text-white border-cyan-600 shadow-md ring-2 ring-cyan-500/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span>🧼 1. Persiapan (Cuci)</span>
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-black {{ $stationCategory === 'PREPARATION' ? 'bg-slate-950/30 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $countPrep }}</span>
                </button>

                <button wire:click="filterStationCategory('SOLING')" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all whitespace-nowrap active:scale-95 flex items-center gap-2 border
                        {{ $stationCategory === 'SOLING' ? 'bg-amber-500 text-slate-950 border-amber-500 shadow-md ring-2 ring-amber-400/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span>🛠️ 2. Soling (Sol Repair)</span>
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-black {{ $stationCategory === 'SOLING' ? 'bg-slate-950/30 text-slate-950' : 'bg-slate-200 text-slate-800' }}">{{ $countSoling }}</span>
                </button>

                <button wire:click="filterStationCategory('UPPER')" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all whitespace-nowrap active:scale-95 flex items-center gap-2 border
                        {{ $stationCategory === 'UPPER' ? 'bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-500/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span>🧵 3. Upper &amp; Jahit</span>
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-black {{ $stationCategory === 'UPPER' ? 'bg-slate-950/30 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $countUpper }}</span>
                </button>

                <button wire:click="filterStationCategory('TREATMENT')" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all whitespace-nowrap active:scale-95 flex items-center gap-2 border
                        {{ $stationCategory === 'TREATMENT' ? 'bg-[#22AF85] text-white border-[#22AF85] shadow-md ring-2 ring-[#22AF85]/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span>🎨 4. Treatment &amp; Repaint</span>
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-black {{ $stationCategory === 'TREATMENT' ? 'bg-slate-950/30 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $countTreatment }}</span>
                </button>

                <button wire:click="filterStationCategory('QC')" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all whitespace-nowrap active:scale-95 flex items-center gap-2 border
                        {{ $stationCategory === 'QC' ? 'bg-purple-600 text-white border-purple-600 shadow-md ring-2 ring-purple-500/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span>🔍 5. Quality Control (QC)</span>
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-black {{ $stationCategory === 'QC' ? 'bg-slate-950/30 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $countQc }}</span>
                </button>
            </div>
        </div>

        {{-- Technician Cards Horizontal Scroll Container --}}
        <div class="relative group">
            {{-- Left Arrow --}}
            <button @click="scrollLeft()" class="hidden sm:flex absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white shadow-lg border border-slate-200 items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 active:scale-95 transition-all">
                ‹
            </button>

            <div x-ref="techScroll" class="flex items-center gap-3.5 overflow-x-auto pb-2 scrollbar-none snap-x transition-all">
                @forelse($technicians as $tech)
                    <button wire:click="selectTechnician({{ $tech->id }})" 
                            class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 transition-all shrink-0 active:scale-95 text-left snap-start
                            {{ $selectedTechnicianId == $tech->id 
                                ? 'bg-[#22AF85] text-white border-[#22AF85] shadow-lg shadow-emerald-950/20 ring-2 ring-[#22AF85]/30' 
                                : 'bg-slate-50 text-slate-700 border-slate-100 hover:bg-slate-100/80 hover:border-slate-200' }}">
                        
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs uppercase shrink-0 shadow-sm
                            {{ $selectedTechnicianId == $tech->id ? 'bg-[#FFC232] text-slate-950 font-black' : 'bg-slate-200 text-slate-700' }}">
                            {{ substr($tech->name, 0, 2) }}
                        </div>
                        
                        <div class="whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-black truncate max-w-[130px] leading-snug">{{ $tech->name }}</p>
                                @if($selectedTechnicianId == $tech->id)
                                    <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="inline-block text-[9px] font-black uppercase tracking-wider px-1.5 py-0.2 rounded-md
                                    {{ $selectedTechnicianId == $tech->id ? 'bg-slate-950/30 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $tech->display_spec }}
                                </span>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="py-4 px-6 text-xs font-bold text-slate-400 italic">
                        Tidak ada personel teknisi pada kategori stasiun ini.
                    </div>
                @endforelse
            </div>

            {{-- Right Arrow --}}
            <button @click="scrollRight()" class="hidden sm:flex absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white shadow-lg border border-slate-200 items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 active:scale-95 transition-all">
                ›
            </button>
        </div>
    </div>

    {{-- Selected Technician KPI Summary Cards --}}
    @if($selectedTech)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            {{-- Running Jobs Card --}}
            <div class="bg-gradient-to-br from-amber-500/10 via-amber-50 to-orange-50/40 p-5 sm:p-6 rounded-3xl border border-amber-200/80 shadow-xs relative overflow-hidden group hover:shadow-md transition-all">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-amber-900">Sedang Dikerjakan</span>
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-black text-amber-950 tracking-tight">{{ $runningCount }}</span>
                    <span class="text-xs font-black text-amber-700 uppercase tracking-wider">SPK Active</span>
                </div>
            </div>

            {{-- Assigned Queue Card --}}
            <div class="bg-gradient-to-br from-blue-500/10 via-blue-50 to-indigo-50/40 p-5 sm:p-6 rounded-3xl border border-blue-200/80 shadow-xs group hover:shadow-md transition-all">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-blue-900">Antrean Penugasan</span>
                    <span class="text-lg">📋</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-black text-blue-950 tracking-tight">{{ $assignedCount }}</span>
                    <span class="text-xs font-black text-blue-700 uppercase tracking-wider">SPK Queue</span>
                </div>
            </div>

            {{-- Completed Jobs Card --}}
            <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-50 to-teal-50/40 p-5 sm:p-6 rounded-3xl border border-emerald-200/80 shadow-xs group hover:shadow-md transition-all">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-emerald-900">
                        Selesai {{ $historyPeriod === 'today' ? 'Hari Ini' : ($historyPeriod === 'month' ? 'Bulan Ini' : 'Seluruhnya') }}
                    </span>
                    <span class="text-lg">✅</span>
                </div>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-3xl sm:text-4xl font-black text-emerald-950 tracking-tight">{{ $completedUniqueSpkCount }}</span>
                    <span class="text-xs font-black text-emerald-700 uppercase tracking-wider">SPK DONE</span>
                </div>
                <div class="flex items-center justify-between text-[10px] font-bold text-emerald-700 pt-2 border-t border-emerald-200/60">
                    <span>{{ $completedTotalJasaCount }} Sub-Jasa</span>
                    <div class="flex items-center gap-1 bg-white/80 rounded-lg p-0.5 border border-emerald-200/80">
                        <button wire:click="$set('historyPeriod', 'today')" class="px-2 py-0.5 rounded-md text-[9px] font-black {{ $historyPeriod === 'today' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:text-slate-900' }}">Hari Ini</button>
                        <button wire:click="$set('historyPeriod', 'all')" class="px-2 py-0.5 rounded-md text-[9px] font-black {{ $historyPeriod === 'all' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:text-slate-900' }}">Semua</button>
                    </div>
                </div>
            </div>

            {{-- Avg Duration Card --}}
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1a3b34] p-5 sm:p-6 rounded-3xl border border-slate-800 shadow-md text-white group hover:shadow-xl transition-all">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-[#FFC232]">Rata-Rata Durasi</span>
                    <span class="text-lg">⏱️</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-black text-[#FFC232] tracking-tight">{{ $avgDuration }}</span>
                    <span class="text-xs font-black text-slate-300 uppercase tracking-wider">Menit / Jasa</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabs & Main Content Area --}}
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-100 space-y-6">
        <div class="bg-white rounded-3xl p-3 border border-slate-200 shadow-xs flex flex-col md:flex-row gap-4 items-center justify-between">
            {{-- Tabs --}}
            <div class="flex items-center gap-2 bg-slate-100/80 p-1.5 rounded-2xl w-full md:w-auto overflow-x-auto">
                <button wire:click="switchTab('running')" 
                        class="flex-1 px-5 py-2.5 rounded-xl text-xs font-black transition-all whitespace-nowrap flex items-center justify-center gap-2 active:scale-95
                        {{ $activeTab === 'running' ? 'bg-[#22AF85] text-white shadow-md font-black' : 'text-slate-600 hover:text-slate-900 font-extrabold' }}">
                    <span>Sedang Dikerjakan ({{ $runningCount }})</span>
                </button>

                <button wire:click="switchTab('assigned')" 
                        class="flex-1 px-5 py-2.5 rounded-xl text-xs font-black transition-all whitespace-nowrap flex items-center justify-center gap-2 active:scale-95
                        {{ $activeTab === 'assigned' ? 'bg-[#FFC232] text-slate-950 shadow-md font-black' : 'text-slate-600 hover:text-slate-900 font-extrabold' }}">
                    <span>Antrean Penugasan ({{ $assignedCount }})</span>
                </button>

                <button wire:click="switchTab('history')" 
                        class="flex-1 px-5 py-2.5 rounded-xl text-xs font-black transition-all whitespace-nowrap flex items-center justify-center gap-2 active:scale-95
                        {{ $activeTab === 'history' ? 'bg-[#FFC232] text-slate-950 shadow-md font-black' : 'text-slate-600 hover:text-slate-900 font-extrabold' }}">
                    <span>Riwayat Selesai ({{ $completedUniqueSpkCount }} SPK)</span>
                </button>
            </div>

            {{-- Search Input --}}
            <div class="relative min-w-[260px]">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. SPK / Pelanggan..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-[#22AF85] transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        {{-- Services / Jobs Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($displayJobs as $job)
                <div class="bg-slate-50/80 rounded-3xl p-6 border border-slate-200/90 hover:border-[#22AF85] transition-all duration-300 flex flex-col justify-between space-y-5 hover:shadow-lg group">
                    <div class="space-y-4">
                        {{-- Top Header: SPK Number & Status --}}
                        <div class="flex justify-between items-start gap-2 border-b border-slate-200/60 pb-3">
                            <div>
                                <span class="text-base font-black text-[#1a3b34] tracking-tight uppercase group-hover:text-[#22AF85] transition-colors block">
                                    {{ $job['spk_number'] }}
                                </span>
                                <span class="inline-block text-[9px] font-black text-[#22AF85] uppercase tracking-wider bg-[#22AF85]/10 px-2 py-0.5 rounded-md mt-1">
                                    STASIUN: {{ $job['station_label'] }}
                                </span>
                            </div>

                            <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded-xl border shadow-2xs shrink-0
                                {{ $job['started_at'] && !$job['completed_at'] ? 'bg-amber-400 text-slate-950 border-amber-500 font-black' : ($job['completed_at'] ? 'bg-emerald-500 text-white border-emerald-600 font-black' : 'bg-blue-100 text-blue-900 border-blue-200 font-bold') }}">
                                {{ $job['started_at'] && !$job['completed_at'] ? 'IN_PROGRESS' : ($job['completed_at'] ? 'COMPLETED' : 'ASSIGNED') }}
                            </span>
                        </div>

                        {{-- Service Title --}}
                        <h4 class="text-sm font-black text-slate-900 leading-snug">
                            {{ $job['service_title'] }}
                        </h4>

                        {{-- Customer & Shoe Info --}}
                        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/60 space-y-1">
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $job['customer_name'] ?: '-' }}</p>
                            <p class="text-[11px] font-extrabold text-slate-500 uppercase flex items-center gap-1">
                                <span>👟</span>
                                <span>{{ $job['shoe_brand'] ?? '' }} {{ $job['shoe_type'] ?? '' }}</span>
                            </p>
                        </div>

                        {{-- LIVE TIMER STOPWATCH PANEL (For Running / In Progress) --}}
                        @if($job['started_at'] && !$job['completed_at'])
                            <div class="p-4 bg-slate-950 rounded-2xl text-white border border-slate-800 text-center space-y-1 shadow-inner"
                                 x-data="stopwatchTimer('{{ $job['started_at']->toIso8601String() }}')" x-init="startTimer()">
                                <div class="flex items-center justify-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-ping"></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-[#FFC232]">Live Running Timer</span>
                                </div>
                                <div class="font-mono text-3xl font-black text-white tracking-widest drop-shadow-md" x-text="formattedTime">00:00:00</div>
                                <span class="text-[9px] text-slate-400 font-extrabold block">Mulai Pengerjaan: {{ $job['started_at']->format('H:i:s') }} WIB</span>
                            </div>
                        @elseif($job['completed_at'])
                            <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200/80 text-emerald-900 text-xs font-bold space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 font-extrabold">Total Durasi:</span>
                                    <span class="font-black text-emerald-950 text-sm">{{ $job['started_at'] ? max(1, $job['started_at']->diffInMinutes($job['completed_at'])) : 1 }} Menit</span>
                                </div>
                                <div class="text-[9px] text-emerald-700 font-bold">
                                    🕒 {{ $job['started_at']?->format('H:i') }} - {{ $job['completed_at']->format('H:i') }} WIB
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Actions Button --}}
                    <div class="pt-3 border-t border-slate-200/60">
                        @if($job['started_at'] && !$job['completed_at'])
                            <button wire:click="completeJob({{ $job['work_order_id'] }}, '{{ $job['station_type'] }}')" 
                                    class="w-full py-3 px-5 bg-[#22AF85] hover:bg-[#1a8a68] text-white font-black text-xs rounded-2xl shadow-md active:scale-95 transition-all uppercase tracking-wider flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Selesaikan Pengerjaan</span>
                            </button>
                        @elseif(!$job['started_at'])
                            <button wire:click="startJob({{ $job['work_order_id'] }}, '{{ $job['station_type'] }}')" 
                                    class="w-full py-3.5 px-5 bg-[#FFC232] hover:bg-yellow-400 text-slate-950 font-black text-xs rounded-2xl shadow-md active:scale-95 transition-all uppercase tracking-wider flex items-center justify-center gap-2.5">
                                <svg class="w-5 h-5 shrink-0 text-slate-950" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span>Mulai Pengerjaan</span>
                            </button>
                        @else
                            <div class="text-center py-2.5 text-xs font-black text-slate-400 italic">Pengerjaan Selesai ✅</div>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Rich Empty State Box --}}
                <div class="col-span-full py-16 px-6 text-center bg-slate-50/50 rounded-3xl border-2 border-dashed border-slate-200/80 space-y-3">
                    <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-[#22AF85] flex items-center justify-center mx-auto text-2xl font-black shadow-inner">
                        🛠️
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Tidak Ada Data Pengerjaan</h3>
                        <p class="text-xs font-bold text-slate-400 max-w-sm mx-auto">
                            Belum ada pengerjaan SPK pada tab <span class="text-slate-700 underline font-black">{{ strtoupper($activeTab) }}</span> untuk teknisi ini. Silakan pilih personel teknisi lain atau ganti tab antrean.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    /* Utility to hide horizontal scrollbar for clean UI */
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-none {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('stopwatchTimer', (startedAtIso) => ({
            startedAt: startedAtIso ? new Date(startedAtIso) : null,
            formattedTime: '00:00:00',
            timerInterval: null,
            startTimer() {
                if (!this.startedAt) return;
                this.updateTimer();
                this.timerInterval = setInterval(() => this.updateTimer(), 1000);
            },
            updateTimer() {
                const now = new Date();
                const diffMs = Math.max(0, now - this.startedAt);
                const totalSeconds = Math.floor(diffMs / 1000);
                const hrs = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                const mins = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                const secs = String(totalSeconds % 60).padStart(2, '0');
                this.formattedTime = `${hrs}:${mins}:${secs}`;
            }
        }));
    });
</script>
