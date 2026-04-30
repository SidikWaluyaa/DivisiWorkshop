<div class="min-h-screen bg-[#f8fafc] pb-20">
    {{-- Header Section --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div class="space-y-3">
                <nav class="flex items-center gap-2 text-[10px] font-black text-[#22AF85] uppercase tracking-[0.3em] mb-1">
                    <a href="#" class="hover:opacity-80 transition-opacity">Workshop</a>
                    <svg class="w-2.5 h-2.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    <span class="opacity-50">Guarantee Management</span>
                </nav>
                <h1 class="text-5xl font-black text-[#1a3b34] tracking-tighter leading-none">
                    Sistem <span class="text-[#22AF85]">Garansi</span>
                </h1>
                <p class="text-sm font-bold text-gray-500 max-w-lg">Kelola klaim perbaikan dan riwayat garansi pelanggan dengan standar premium.</p>
            </div>

            <div class="flex items-center gap-4">
                <button wire:click="openCreateModal" 
                        class="flex items-center gap-3 px-8 py-4 bg-[#FFC232] text-[#1a3b34] rounded-2xl shadow-xl shadow-yellow-500/20 hover:shadow-yellow-500/40 hover:-translate-y-0.5 transition-all duration-300 font-extrabold uppercase tracking-widest text-[11px]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    Buat Klaim Baru
                </button>
            </div>
        </div>

        {{-- Analytics Widgets --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            {{-- Total Claims --}}
            <div class="bg-[#1a3b34] rounded-[2.5rem] p-8 text-white shadow-xl shadow-teal-900/10 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-colors"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#22AF85] mb-2">Total Klaim</p>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-4xl font-black tabular-nums">{{ number_format($stats['total']) }}</h2>
                        <span class="text-[10px] font-bold opacity-50 uppercase tracking-widest">Garansi</span>
                    </div>
                </div>
            </div>

            {{-- Open Claims --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between relative group hover:shadow-lg transition-all">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Sedang Proses</p>
                    <h2 class="text-4xl font-black text-[#1a3b34] tabular-nums">{{ number_format($stats['open']) }}</h2>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center px-4 py-1.5 bg-yellow-50 text-[10px] font-black uppercase tracking-widest text-[#FFC232] rounded-xl border border-yellow-100">
                        OPEN CLAIMS
                    </span>
                </div>
            </div>

            {{-- Finished --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between relative group hover:shadow-lg transition-all">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Sudah Selesai</p>
                    <h2 class="text-4xl font-black text-[#1a3b34] tabular-nums">{{ number_format($stats['finished']) }}</h2>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center px-4 py-1.5 bg-green-50 text-[10px] font-black uppercase tracking-widest text-[#22AF85] rounded-xl border border-teal-100">
                        FINISHED
                    </span>
                </div>
            </div>

            {{-- Top Category --}}
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between relative group hover:shadow-lg transition-all">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Kanal Terbanyak</p>
                    <h2 class="text-2xl font-black text-[#1a3b34] truncate uppercase tracking-tighter">{{ $stats['topCategory'] }}</h2>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center px-4 py-1.5 bg-teal-50 text-[10px] font-black uppercase tracking-widest text-[#22AF85] rounded-xl border border-teal-100">
                        TOP CHANNEL
                    </span>
                </div>
            </div>
        </div>

        {{-- Dynamic Filter Bar --}}
        <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100 mb-10">
            <div class="flex flex-col lg:flex-row items-end gap-6">
                {{-- Date From --}}
                <div class="w-full lg:w-auto flex-1 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Dari Tanggal</label>
                    <input type="date" wire:model.live="dateFrom" 
                           class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#22AF85]/20 focus:bg-white transition-all">
                </div>

                {{-- Date To --}}
                <div class="w-full lg:w-auto flex-1 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Sampai Tanggal</label>
                    <input type="date" wire:model.live="dateTo" 
                           class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#22AF85]/20 focus:bg-white transition-all">
                </div>

                {{-- PIC Filter --}}
                <div class="w-full lg:w-auto flex-1 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">PIC Pembuat</label>
                    <select wire:model.live="filterPic" 
                            class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#22AF85]/20 focus:bg-white transition-all appearance-none">
                        <option value="">Semua PIC</option>
                        @foreach($availablePics as $pic)
                            <option value="{{ $pic->id }}">{{ $pic->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Category Filter --}}
                <div class="w-full lg:w-auto flex-1 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-2">Kanal SPK</label>
                    <select wire:model.live="filterCategory" 
                            class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#22AF85]/20 focus:bg-white transition-all appearance-none">
                        <option value="">Semua Kanal</option>
                        @foreach($availableCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset Button --}}
                <button wire:click="resetFilters" 
                        class="w-full lg:w-auto px-8 py-4 bg-gray-100 text-gray-400 hover:bg-[#22AF85] hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                    Reset
                </button>
            </div>
        </div>

        {{-- Tabs & Secondary Search --}}
        <div class="bg-white rounded-[2.5rem] p-4 shadow-sm border border-gray-100 mb-10 flex flex-col lg:flex-row items-center justify-between gap-6">
            {{-- Tab Switcher --}}
            <div class="flex p-1.5 bg-gray-50 rounded-2xl w-full lg:w-auto">
                <button wire:click="switchTab('active')" 
                        class="flex-1 lg:flex-none px-8 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-300 {{ $activeTab === 'active' ? 'bg-white text-[#22AF85] shadow-md' : 'text-gray-400 hover:text-gray-600' }}">
                    Garansi Aktif
                </button>
                <button wire:click="switchTab('history')" 
                        class="flex-1 lg:flex-none px-8 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-300 {{ $activeTab === 'history' ? 'bg-white text-[#22AF85] shadow-md' : 'text-gray-400 hover:text-gray-600' }}">
                    Riwayat Selesai
                </button>
            </div>

            {{-- Global Search --}}
            <div class="relative w-full lg:max-w-md group">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari nomor SPK atau nama customer..." 
                       class="w-full pl-12 pr-6 py-4 bg-gray-50 border-transparent rounded-[1.5rem] text-sm font-bold text-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-[#22AF85]/20 focus:bg-white transition-all duration-300 group-hover:bg-gray-100">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#22AF85] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="relative min-h-[400px]">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                     class="mb-8 flex items-center gap-4 bg-[#22AF85] text-white px-6 py-4 rounded-3xl shadow-lg shadow-teal-500/20 animate-bounce-subtle">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    <p class="text-sm font-black uppercase tracking-wider">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-8">
                @forelse($warranties as $warranty)
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 group border border-gray-50 flex flex-col lg:flex-row gap-10">
                        {{-- Info Primary --}}
                        <div class="lg:w-1/4 space-y-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-1">Guarantee ID</span>
                                <h3 class="text-2xl font-black text-[#1a3b34] tracking-tight group-hover:text-[#22AF85] transition-colors uppercase">
                                    {{ $warranty->garansi_spk_number }}
                                </h3>
                                <div class="mt-2 inline-flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Asli: {{ $warranty->workOrder->spk_number }}
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-50">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ $activeTab === 'active' ? 'Created At' : 'Finished At' }}</span>
                                <p class="text-xs font-black text-gray-700">
                                    {{ ($activeTab === 'active' ? $warranty->created_at : $warranty->finished_at)->format('d M Y') }}
                                    <span class="text-gray-300 mx-1">•</span>
                                    {{ ($activeTab === 'active' ? $warranty->created_at : $warranty->finished_at)->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 space-y-6">
                            <div class="flex flex-col md:flex-row justify-between gap-6">
                                <div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Customer & Item</span>
                                    <div class="flex flex-col">
                                        <p class="text-lg font-black text-[#1a3b34] uppercase tracking-tight">{{ $warranty->workOrder->customer_name }}</p>
                                        @if($warranty->workOrder->customer_phone)
                                            <p class="text-[11px] font-bold text-gray-500 mt-0.5 tracking-wider">{{ $warranty->workOrder->customer_phone }}</p>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-[#22AF85] mt-1">{{ $warranty->workOrder->shoe_brand }} <span class="text-gray-300 mx-1">/</span> {{ $warranty->workOrder->shoe_type }}</p>
                                </div>
                                <div class="text-right">
                                    @if($activeTab === 'active')
                                        <span class="inline-flex items-center px-4 py-2 bg-orange-50 text-[10px] font-black uppercase tracking-widest text-[#FFC232] rounded-2xl border border-yellow-100 animate-pulse">
                                            In Workshop Process
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-2 bg-green-50 text-[10px] font-black uppercase tracking-widest text-[#22AF85] rounded-2xl border border-teal-100">
                                            Guarantee Completed
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-gray-50/80 rounded-3xl p-6 border border-gray-100 relative group/notes overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-full bg-[#22AF85]/5 -skew-x-12 translate-x-12"></div>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-3">
                                    <svg class="w-3.5 h-3.5 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    Guarantee Repair Notes
                                </span>
                                <p class="text-sm font-bold text-gray-700 leading-relaxed relative z-10 whitespace-pre-line">{{ $warranty->description }}</p>
                                
                                {{-- Meta Info (Creator/Finisher) --}}
                                <div class="mt-4 pt-4 border-t border-gray-200/50 flex items-center justify-between">
                                    <div class="flex items-center gap-2 opacity-60">
                                        <div class="w-6 h-6 rounded-full bg-[#22AF85] text-white flex items-center justify-center text-[10px] font-black">
                                            {{ substr($warranty->creator->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">By {{ $warranty->creator->name ?? 'System' }}</span>
                                    </div>
                                    @if($activeTab === 'history' && $warranty->finisher)
                                        <div class="flex items-center gap-2 opacity-60">
                                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Finalized By {{ $warranty->finisher->name }}</span>
                                            <div class="w-6 h-6 rounded-full bg-gray-700 text-white flex items-center justify-center text-[10px] font-black">
                                                {{ substr($warranty->finisher->name, 0, 1) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Involved Technicians (Categorized Team) --}}
                                <div class="mt-4 pt-4 border-t border-gray-200/30 space-y-2">
                                    {{-- PREP --}}
                                    @php $prepTechs = $warranty->workOrder->prep_technicians; @endphp
                                    @if($prepTechs->count() > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="w-8 text-[7px] font-black text-gray-400 uppercase tracking-tighter">Prep</span>
                                            <div class="flex -space-x-1.5">
                                                @foreach($prepTechs->take(3) as $tech)
                                                    <div class="w-5 h-5 rounded-full bg-white border border-gray-100 flex items-center justify-center text-[7px] font-black text-blue-500 shadow-sm overflow-hidden" title="Prep: {{ $tech->name }}">
                                                        {{ substr($tech->name, 0, 1) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            <span class="text-[8px] font-bold text-gray-600 truncate tracking-tight">{{ $prepTechs->pluck('name')->implode(', ') }}</span>
                                        </div>
                                    @endif

                                    {{-- PROD --}}
                                    @php $prodTechs = $warranty->workOrder->prod_technicians; @endphp
                                    @if($prodTechs->count() > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="w-8 text-[7px] font-black text-gray-400 uppercase tracking-tighter">Prod</span>
                                            <div class="flex -space-x-1.5">
                                                @foreach($prodTechs->take(3) as $tech)
                                                    <div class="w-5 h-5 rounded-full bg-white border border-gray-100 flex items-center justify-center text-[7px] font-black text-orange-500 shadow-sm overflow-hidden" title="Prod: {{ $tech->name }}">
                                                        {{ substr($tech->name, 0, 1) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            <span class="text-[8px] font-bold text-gray-600 truncate tracking-tight">{{ $prodTechs->pluck('name')->implode(', ') }}</span>
                                        </div>
                                    @endif

                                    {{-- QC --}}
                                    @php $qcTechs = $warranty->workOrder->qc_technicians; @endphp
                                    @if($qcTechs->count() > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="w-8 text-[7px] font-black text-gray-400 uppercase tracking-tighter">QC</span>
                                            <div class="flex -space-x-1.5">
                                                @foreach($qcTechs->take(3) as $tech)
                                                    <div class="w-5 h-5 rounded-full bg-white border border-gray-100 flex items-center justify-center text-[7px] font-black text-purple-500 shadow-sm overflow-hidden" title="QC: {{ $tech->name }}">
                                                        {{ substr($tech->name, 0, 1) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            <span class="text-[8px] font-bold text-gray-600 truncate tracking-tight">{{ $qcTechs->pluck('name')->implode(', ') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="lg:w-[180px] flex flex-col justify-center gap-3">
                            <a href="{{ route('garansi.print', $warranty->id) }}" target="_blank"
                               class="w-full flex items-center justify-center gap-2 py-4 bg-white border-2 border-gray-100 hover:border-[#22AF85]/30 hover:bg-[#22AF85]/5 shadow-sm rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-600 hover:text-[#22AF85] transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Print SPK
                            </a>
                            @if($activeTab === 'active')
                                <button wire:click="finishWarranty({{ $warranty->id }})" 
                                        onclick="confirm('Selesaikan perbaikan garansi ini?') || event.stopImmediatePropagation()"
                                        class="w-full flex items-center justify-center gap-2 py-4 bg-[#22AF85] hover:bg-[#1a8b68] shadow-lg shadow-teal-500/20 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Selesai QC
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-24 flex flex-col items-center justify-center bg-white rounded-[4rem] border-2 border-dashed border-gray-100 italic">
                        <div class="w-24 h-24 bg-[#22AF85]/5 rounded-full flex items-center justify-center mb-6 text-4xl">✨</div>
                        <p class="text-sm font-black text-[#1a3b34] uppercase tracking-widest mb-1">Antrean Garansi Bersih</p>
                        <p class="text-[11px] font-bold text-gray-400">Tidak ada pengerjaan garansi yang {{ $activeTab === 'active' ? 'tertunda' : 'ditemukan' }}.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $warranties->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL REDESIGN --}}
    <div 
        x-data="{ show: @entangle('showCreateModal') }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        class="fixed inset-0 z-[60] overflow-y-auto"
        style="display: none;"
    >
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-[#1a3b34]/80 backdrop-blur-md transition-opacity" aria-hidden="true" @click="show = false"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-12 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 class="relative bg-white rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-gray-100" @click.stop>
                
                <div class="px-10 pt-10 pb-8">
                    <div class="flex justify-between items-start mb-10">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest text-[#22AF85]">Langkah {{ $step }} dari 2</span>
                            <h3 class="text-3xl font-black text-[#1a3b34] tracking-tighter">
                                @if($step === 1) Cari <span class="text-[#22AF85]">Data Asli</span> @else Deskripsi <span class="text-[#22AF85]">Masalah</span> @endif
                            </h3>
                        </div>
                        <button @click="show = false" class="p-3 bg-gray-50 text-gray-400 hover:text-red-500 rounded-2xl transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    @if($step === 1)
                        <div class="space-y-6">
                            <div class="relative group">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#22AF85] mb-3 ml-2">Cari SPK Asli atau Nama Pelanggan</label>
                                <input type="text" wire:model.live.debounce.500ms="searchSpk" placeholder="Ketik minimal 3 huruf..." 
                                       class="w-full px-8 py-5 bg-gray-50 border-transparent rounded-[2rem] text-sm font-bold text-gray-700 placeholder:text-gray-400 focus:ring-4 focus:ring-[#22AF85]/10 focus:bg-white transition-all duration-300">
                                <div wire:loading wire:target="searchSpk" class="absolute right-6 top-[54px]">
                                    <svg class="animate-spin h-5 w-5 text-[#22AF85]" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            </div>

                            <div class="space-y-3 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                                @forelse($searchSource as $wo)
                                    <div wire:click.prevent="selectWorkOrder({{ $wo->id }})" 
                                         class="group/item bg-gray-100 hover:bg-white hover:shadow-xl hover:shadow-[#22AF85]/10 border-2 border-transparent hover:border-[#22AF85]/30 p-5 rounded-[2rem] cursor-pointer transition-all duration-300 flex items-center gap-6">
                                        <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-200 shrink-0 border border-white shadow-sm">
                                            @php
                                                $photo = $wo->photos->whereIn('step', ['QC_FINAL', 'SELESAI', 'FINISH'])->first();
                                                $pUrl = $photo ? $photo->photo_url : 'https://placehold.co/400x400?text=No+Photo';
                                            @endphp
                                            <img src="{{ $pUrl }}" class="w-full h-full object-cover group-hover/item:scale-110 transition-transform duration-500">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-0.5">
                                                <div class="flex flex-col gap-1">
                                                    <span class="font-black text-[#1a3b34] text-xs group-hover/item:text-[#22AF85] transition-colors uppercase tracking-tight">{{ $wo->spk_number }}</span>
                                                    @if($wo->warranties->count() > 0)
                                                        <span class="inline-flex items-center gap-1 text-[8px] font-black text-[#FFC232] bg-yellow-50 px-2 py-0.5 rounded-md border border-yellow-100 uppercase tracking-tighter">
                                                            ⚠️ Pernah Garansi ({{ $wo->warranties->count() }}x)
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-[9px] font-black text-[#22AF85] bg-teal-50 px-2 py-1 rounded-lg border border-teal-100 uppercase tracking-widest">
                                                    AMBIL: {{ $wo->taken_date ? $wo->taken_date->format('d/m/y') : '-' }}
                                                </span>
                                            </div>
                                            <h4 class="text-sm font-extrabold text-gray-700 truncate capitalize leading-none">{{ strtolower($wo->customer_name) }}</h4>
                                            <p class="text-[10px] font-bold text-gray-400 mt-1 truncate uppercase tracking-widest">{{ $wo->shoe_brand }} • {{ $wo->shoe_type }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center bg-gray-50 rounded-[2rem] italic">
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tidak ada pesanan yang cocok atau sudah diambil.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="space-y-8">
                            {{-- Item Summary --}}
                            @if($this->selectedWorkOrder)
                                <div class="bg-[#1a3b34] rounded-[2.5rem] p-6 shadow-xl text-white flex gap-6 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-32 h-full bg-[#22AF85]/20 -skew-x-12 translate-x-12"></div>
                                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-white/5 shrink-0 border border-white/10">
                                        @php
                                            $photo = $this->selectedWorkOrder->photos->whereIn('step', ['QC_FINAL', 'SELESAI', 'FINISH'])->first();
                                            $pUrl = $photo ? $photo->photo_url : 'https://placehold.co/400x400?text=No+Photo';
                                        @endphp
                                        <img src="{{ $pUrl }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-xl font-black tracking-tight mb-1">{{ $this->selectedWorkOrder->spk_number }}</h4>
                                        <p class="text-xs font-bold text-[#FFC232] uppercase tracking-widest mb-3">{{ $this->selectedWorkOrder->customer_name }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($this->selectedWorkOrder->workOrderServices as $svc)
                                                <span class="text-[9px] font-black bg-white/10 px-2 py-1 rounded-lg border border-white/5 uppercase tracking-tighter">
                                                    {{ $svc->custom_service_name ?? ($svc->service->name ?? '-') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Input --}}
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-[#22AF85] mb-3 ml-2">Detail Kerusakan (Bahasa Manusia)</label>
                                    <textarea wire:model="description" rows="5" placeholder="Tuliskan alasannya, misal: Lem solnya copot lagi kak..." 
                                              class="w-full px-8 py-6 bg-gray-50 border-transparent rounded-[2rem] text-sm font-bold text-gray-700 placeholder:text-gray-400 focus:ring-4 focus:ring-[#22AF85]/10 focus:bg-white transition-all duration-300"></textarea>
                                    @error('description') <span class="text-red-500 text-[10px] font-black uppercase tracking-widest ml-4">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50/50 px-10 py-8 flex flex-col sm:flex-row-reverse gap-4">
                    @if($step === 2)
                        <button wire:click="saveWarranty" class="flex-1 bg-[#FFC232] text-[#1a3b34] py-5 rounded-2xl shadow-xl shadow-yellow-500/20 hover:shadow-yellow-500/40 hover:-translate-y-0.5 transition-all text-xs font-black uppercase tracking-[0.2em] animate-pulse">
                            Selesaikan & Cetak SPK
                        </button>
                    @endif
                    <button @click="show = false" class="flex-1 bg-white border-2 border-gray-100 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                        Tutup
                    </button>
                    @if($step === 2)
                        <button wire:click="$set('step', 1)" class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-[#22AF85] hover:opacity-70 transition-all">
                            Kembali
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-bounce-subtle {
        animation: bounce-subtle 3s infinite;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

