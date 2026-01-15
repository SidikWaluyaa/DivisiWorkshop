{{-- Period Filter Component --}}
<div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100 mb-6 relative group transform transition-all hover:shadow-xl" 
     x-data="{ filterType: '{{ request()->has('month') ? 'month' : 'year' }}' }">
    
    {{-- Decorative Header --}}
    <div class="bg-gradient-to-r from-teal-600 via-emerald-600 to-teal-500 px-6 py-4 flex items-center justify-between">
        <h3 class="text-white font-bold text-lg flex items-center gap-2">
            <svg class="w-6 h-6 text-yellow-300 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Filter Periode Data
        </h3>
        
        @if(isset($periodLabel))
        <div class="hidden sm:flex items-center">
            <span class="inline-flex items-center px-4 py-1.5 bg-white/20 backdrop-blur-sm border border-white/30 text-white rounded-full text-xs font-bold shadow-sm uppercase tracking-wider">
                <svg class="w-3 h-3 mr-1.5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $periodLabel }}
            </span>
        </div>
        @endif
    </div>

    <div class="p-6">
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col md:flex-row gap-6 items-end">
            
            {{-- Mode Toggle --}}
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mode Tampilan</label>
                <div class="bg-gray-100 p-1.5 rounded-xl inline-flex w-full md:w-auto">
                    <button type="button" 
                            @click="filterType = 'month'" 
                            :class="filterType === 'month' ? 'bg-white text-teal-700 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bulanan
                    </button>
                    <button type="button" 
                            @click="filterType = 'year'" 
                            :class="filterType === 'year' ? 'bg-white text-teal-700 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tahunan
                    </button>
                    
                    {{-- Hidden inputs for form submission --}}
                    <input type="radio" name="mode" value="month" x-model="filterType" class="hidden">
                    <input type="radio" name="mode" value="year" x-model="filterType" class="hidden">
                    
                    {{-- Preserve previous selection if switching --}}
                    @if(request('month')) <input type="hidden" name="month" value="{{ request('month') }}" :disabled="filterType !== 'month'"> @endif
                    @if(request('year')) <input type="hidden" name="year" value="{{ request('year') }}" :disabled="filterType !== 'year'"> @endif
                </div>
            </div>
            
            {{-- Inputs --}}
            <div class="flex-1 w-full md:max-w-xs transition-all duration-300 transform origin-left" 
                 x-show="filterType === 'month'" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak>
                <label for="month" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Periode</label>
                <div class="relative">
                    <input type="month" 
                           name="month" 
                           id="month" 
                           value="{{ request('month', now()->format('Y-m')) }}" 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 transition-all font-medium text-gray-700">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 w-full md:max-w-xs transition-all duration-300 transform origin-left" 
                 x-show="filterType === 'year'" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak>
                <label for="year" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Tahun</label>
                <div class="relative">
                    <select name="year" 
                            id="year" 
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 transition-all font-medium text-gray-700 appearance-none bg-no-repeat bg-[right_1rem_center]">
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex gap-3 w-full md:w-auto pt-2">
                <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl shadow-lg shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 font-bold flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Terapkan
                </button>
                
                @if(request()->has('month') || request()->has('year'))
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-600 rounded-xl transition-all font-semibold flex items-center justify-center gap-2" title="Reset Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>
