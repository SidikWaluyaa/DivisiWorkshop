<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 font-sans">
    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-[#1a3b34] via-[#22AF85] to-[#1a3b34] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-emerald-400/20">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/10 rounded-full border border-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-[#FFC232] shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                    <span>ADMINISTRASI &amp; SKILL MANAGEMENT</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight leading-tight">
                    Manajemen Stasiun &amp; <span class="text-[#FFC232]">Skill Jasa Teknisi</span>
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 font-medium max-w-xl leading-relaxed">
                    Atur stasiun kerja utama teknisi dan pemetaan master jasa spesifik (67 Services) yang dikuasai untuk otomasi penugasan SPK yang presisi.
                </p>
            </div>

            @if(session()->has('success'))
                <div class="p-4 bg-slate-900/90 border border-emerald-400/40 backdrop-blur-md rounded-2xl text-white text-xs font-bold flex items-center gap-3 shadow-xl">
                    <div class="w-7 h-7 rounded-xl bg-[#22AF85] flex items-center justify-center text-slate-950 font-black shrink-0 text-xs">✓</div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Personnel Selector Container --}}
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-100 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#22AF85]"></span>
                <span class="text-xs font-black uppercase tracking-wider text-slate-800">Pilih Personel Teknisi</span>
            </div>

            {{-- Station Filter --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button wire:click="$set('stationFilter', 'ALL')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap
                        {{ $stationFilter === 'ALL' ? 'bg-[#22AF85] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🌟 Semua
                </button>
                <button wire:click="$set('stationFilter', 'PREPARATION')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap
                        {{ $stationFilter === 'PREPARATION' ? 'bg-cyan-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🧼 Prep
                </button>
                <button wire:click="$set('stationFilter', 'SOLING')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap
                        {{ $stationFilter === 'SOLING' ? 'bg-amber-500 text-slate-950 shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🛠️ Soling
                </button>
                <button wire:click="$set('stationFilter', 'UPPER')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap
                        {{ $stationFilter === 'UPPER' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🧵 Upper
                </button>
                <button wire:click="$set('stationFilter', 'TREATMENT')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap
                        {{ $stationFilter === 'TREATMENT' ? 'bg-[#22AF85] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🎨 Treatment
                </button>
                <button wire:click="$set('stationFilter', 'QC')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap
                        {{ $stationFilter === 'QC' ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    🔍 QC
                </button>
            </div>
        </div>

        {{-- Technicians Horizontal Scroll list --}}
        <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none snap-x">
            @foreach($technicians as $tech)
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
                        <span class="inline-block text-[9px] font-black uppercase tracking-wider px-1.5 py-0.2 rounded-md mt-0.5
                            {{ $selectedTechnicianId == $tech->id ? 'bg-slate-950/30 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ $tech->station ?: ($tech->specialization ?: 'TEKNISI') }}
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Selected Technician Skill Configuration Box --}}
    @if($selectedTech)
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-100 space-y-6">
            {{-- Stasiun Utama Selector Card --}}
            <div class="p-5 bg-gradient-to-r from-slate-900 to-[#1a3b34] rounded-2xl text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#FFC232] text-slate-950 flex items-center justify-center text-lg font-black shrink-0 shadow-md">
                        {{ substr($selectedTech->name, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white leading-snug">{{ $selectedTech->name }}</h3>
                        <p class="text-xs font-bold text-emerald-300">
                            Total Jasa Dikuasai: <span class="text-[#FFC232] font-black text-sm">{{ count($assignedServiceIds) }} Services</span>
                        </p>
                    </div>
                </div>

                {{-- Stasiun Selector Radio Pills --}}
                <div class="space-y-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-300 block">Stasiun Kerja Utama:</span>
                    <div class="flex flex-wrap gap-1.5">
                        <button wire:click="updateStation('PREPARATION')" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all {{ $selectedStation === 'PREPARATION' ? 'bg-cyan-500 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                            🧼 Preparation
                        </button>
                        <button wire:click="updateStation('SOLING')" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all {{ $selectedStation === 'SOLING' ? 'bg-amber-400 text-slate-950 shadow-md font-black' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                            🛠️ Soling
                        </button>
                        <button wire:click="updateStation('UPPER')" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all {{ $selectedStation === 'UPPER' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                            🧵 Upper
                        </button>
                        <button wire:click="updateStation('TREATMENT')" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all {{ $selectedStation === 'TREATMENT' ? 'bg-[#22AF85] text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                            🎨 Treatment
                        </button>
                        <button wire:click="updateStation('QC')" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all {{ $selectedStation === 'QC' ? 'bg-purple-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                            🔍 QC
                        </button>
                    </div>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Matrix Master Jasa Sepatu (67 Services)</h4>
                    <p class="text-xs font-bold text-slate-400">Centang jasa yang dapat dikerjakan oleh teknisi ini untuk otomasi penugasan SPK.</p>
                </div>

                <div class="relative min-w-[260px]">
                    <input type="text" wire:model.live.debounce.300ms="searchService" placeholder="Cari nama jasa..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-[#22AF85] transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            {{-- Services Grid Categories --}}
            <div class="space-y-8">
                @foreach($servicesGrouped as $categoryName => $services)
                    <div class="bg-slate-50/80 rounded-3xl p-6 border border-slate-200/80 space-y-4">
                        {{-- Category Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/60 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 bg-[#22AF85]/10 text-[#22AF85] rounded-xl text-xs font-black uppercase tracking-wider">
                                    {{ $categoryName }}
                                </span>
                                <span class="text-xs font-bold text-slate-500">
                                    ({{ count(array_intersect($services->pluck('id')->toArray(), $assignedServiceIds)) }} / {{ $services->count() }} Terpilih)
                                </span>
                            </div>

                            {{-- Select / Deselect All Category Buttons --}}
                            <div class="flex items-center gap-2">
                                <button wire:click="selectAllCategory('{{ $categoryName }}')" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded-xl text-[11px] font-black transition-all active:scale-95">
                                    ✓ Centang Semua
                                </button>
                                <button wire:click="deselectAllCategory('{{ $categoryName }}')" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-[11px] font-bold transition-all active:scale-95">
                                    ✕ Hapus Semua
                                </button>
                            </div>
                        </div>

                        {{-- Service Checkboxes Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($services as $svc)
                                @php
                                    $isAssigned = in_array($svc->id, $assignedServiceIds);
                                @endphp
                                <label wire:click="toggleService({{ $svc->id }})" 
                                       class="flex items-center justify-between p-3.5 rounded-2xl border-2 transition-all cursor-pointer select-none active:scale-98
                                       {{ $isAssigned 
                                           ? 'bg-emerald-50/90 border-[#22AF85] text-slate-900 shadow-xs' 
                                           : 'bg-white border-slate-200/80 text-slate-600 hover:bg-slate-100/60' }}">
                                    
                                    <div class="flex items-center gap-3 min-w-0 pr-2">
                                        <div class="w-5 h-5 rounded-lg border-2 flex items-center justify-center text-xs font-black shrink-0 transition-all
                                            {{ $isAssigned ? 'bg-[#22AF85] border-[#22AF85] text-white' : 'border-slate-300 bg-white' }}">
                                            @if($isAssigned) ✓ @endif
                                        </div>
                                        <div>
                                            <p class="text-xs font-black leading-snug truncate {{ $isAssigned ? 'text-slate-950 font-black' : 'text-slate-700' }}">
                                                {{ $svc->name }}
                                            </p>
                                            <p class="text-[10px] font-bold text-slate-400">
                                                Rp {{ number_format($svc->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($isAssigned)
                                        <span class="px-2 py-0.5 bg-[#22AF85] text-white text-[9px] font-black rounded-md shrink-0">
                                            AKTIF
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
