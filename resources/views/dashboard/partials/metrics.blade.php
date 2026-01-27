<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Revenue Card (GREEN) -->
    <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-[#22AF85]/50 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <svg class="w-24 h-24 text-[#22AF85]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05 1.18 1.91 2.53 1.91 1.33 0 2.26-.87 2.26-2.02 0-1.13-.95-1.58-2.82-2.03-2.03-.49-3.21-1.35-3.21-3.08 0-1.63 1.25-2.88 3.12-3.17V4h2.67v1.92c1.4.3 2.75 1.24 3.01 3.14h-1.92c-.22-1.28-1.28-1.75-2.22-1.75-1.29 0-2.12.87-2.12 1.84 0 1.04 1.12 1.48 2.66 1.84 2.22.53 3.37 1.5 3.37 3.23 0 1.77-1.39 2.94-3.32 3.11z"/></svg>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Pendapatan ({{ $periodLabel }})</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-[#22AF85] transition-colors">
                Rp {{ number_format($revenueData['total'] / 1000, 0, ',', '.') }}<span class="text-lg text-gray-400 font-bold">rb</span>
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-2 py-1 bg-[#22AF85]/10 text-[#22AF85] text-xs font-bold rounded-lg group-hover:bg-[#22AF85]/20 transition-colors">
                    +{{ count($revenueData['daily']['data']) }} Data Point
                </span>
                <span class="text-xs text-gray-400 font-medium tracking-tight">teranalisis</span>
            </div>
        </div>
    </div>

    <!-- Active Orders Card (YELLOW) -->
    <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-[#FFC232]/50 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <svg class="w-24 h-24 text-[#FFC232]" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1.9-2-2-2V4c0-1.1-.9-2-2-2zm-8 18H6V8h6v12zm8 0h-6V8h6v12zM8 4h8v2H8V4z"/></svg>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Order Aktif</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-yellow-600 transition-colors">
                {{ $activeOrdersCount }}
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-[#FFC232] h-1.5 rounded-full" style="width: 70%"></div>
                </div>
                <span class="text-xs text-yellow-600 font-bold tracking-tight">Running</span>
            </div>
        </div>
    </div>

    <!-- Net Profit Card (GREEN) -->
    <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-[#22AF85]/50 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <svg class="w-24 h-24 text-[#22AF85]" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Net Profit ({{ $periodLabel }})</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-[#22AF85] transition-colors">
                Rp {{ number_format($financialMetrics['net_profit'] / 1000, 0, ',', '.') }}<span class="text-lg text-gray-400 font-bold">rb</span>
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-2 py-1 bg-[#22AF85]/10 text-[#22AF85] text-xs font-bold rounded-lg group-hover:bg-[#22AF85]/20 transition-colors">
                    {{ $financialMetrics['margin'] }}% Margin
                </span>
                <span class="text-xs text-gray-400 font-medium">realized</span>
            </div>
        </div>
    </div>

    <!-- Customer Retention Card (GREEN VARIANT) -->
    <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-[#22AF85]/50 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <svg class="w-24 h-24 text-[#22AF85]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Retention Rate</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-[#22AF85] transition-colors">
                {{ $customerRetention['rate'] }}%
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-2 py-1 bg-[#22AF85]/10 text-[#22AF85] text-xs font-bold rounded-lg group-hover:bg-[#22AF85]/20 transition-colors">
                    {{ $customerRetention['returning'] }} Repeat
                </span>
                 <span class="text-xs text-gray-400 font-medium">vs {{ $customerRetention['new'] }} New</span>
            </div>
        </div>
    </div>
</div>
