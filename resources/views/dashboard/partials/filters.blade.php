<div class="sticky top-4 z-50 animate-fade-in-up" x-data="{ showCustom: @js($selectedPeriod === 'custom') }">
    <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl rounded-3xl p-3 flex flex-col md:flex-row items-center justify-between gap-4">
        
        {{-- Preset Filters --}}
        <div class="flex items-center gap-1 bg-gray-100/50 p-1 rounded-2xl overflow-x-auto no-scrollbar max-w-full">
            @php
                $presets = [
                    'today' => 'Hari Ini',
                    '7d' => '7 Hari',
                    '30d' => '30 Hari',
                    'this_month' => 'Bulan Ini',
                    'last_month' => 'Bulan Lalu',
                    'ytd' => 'YTD',
                    'custom' => 'Kustom'
                ];
            @endphp

            @foreach($presets as $key => $label)
                <button 
                    onclick="window.location.href='{{ route('dashboard', ['period' => $key]) }}'"
                    class="px-4 py-2 rounded-xl text-xs font-black transition-all whitespace-nowrap
                        {{ $selectedPeriod === $key 
                            ? 'bg-[#22AF85] text-white shadow-lg shadow-[#22AF85]/30' 
                            : 'text-gray-500 hover:bg-white hover:text-[#22AF85]' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Selected Period Display & Custom Range Picker --}}
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-[#22AF85]/10 rounded-xl border border-[#22AF85]/20">
                <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest block leading-none mb-1">Periode Aktif</span>
                <span class="text-sm font-bold text-gray-800 leading-none">{{ $periodLabel }}</span>
            </div>

            @if($selectedPeriod === 'custom' || true) {{-- Always show or conditional --}}
            <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2" x-show="showCustom" x-cloak>
                <input type="hidden" name="period" value="custom">
                <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] py-2">
                <span class="text-gray-400 font-bold">-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] py-2">
                <button type="submit" class="p-2 bg-gray-900 text-white rounded-xl hover:bg-[#22AF85] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </form>
            @endif
        </div>

    </div>
</div>
