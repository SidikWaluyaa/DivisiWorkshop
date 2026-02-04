<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg" style="background-color: #22AF85">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                    {{ __('CS Performance Analytics') }}
                </h2>
                <p class="text-xs font-bold text-teal-100 tracking-widest uppercase opacity-70">Laporan Konversi & KPI CS</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Filter Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">Periode Laporan</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Gunakan filter untuk merubah rentang data</p>
                </div>
                
                <form action="{{ route('cs.analytics') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 px-4 py-2.5">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Start Date</span>
                            <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                                   class="bg-transparent border-none text-xs font-black text-gray-900 dark:text-white focus:ring-0 p-0">
                        </div>
                        <span class="mx-4 text-gray-300 font-bold">/</span>
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5">End Date</span>
                            <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" 
                                   class="bg-transparent border-none text-xs font-black text-gray-900 dark:text-white focus:ring-0 p-0">
                        </div>
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 dark:shadow-none flex items-center gap-2 font-black text-xs uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Update
                    </button>
                    
                    @if(request()->anyFilled(['start_date', 'end_date']))
                        <a href="{{ route('cs.analytics') }}" class="p-3 bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-2xl hover:bg-gray-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl p-8 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div class="absolute top-0 left-0 w-2 h-full bg-[#22AF85]"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-3">Total Lead Intake</p>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white leading-none tracking-tighter">{{ number_format($overview['total_leads']) }}</h3>
                        <p class="mt-6 text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 inline-block px-3 py-1.5 rounded-full">Input periode ini</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl p-8 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity text-[#22AF85]">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </div>
                    <div class="absolute top-0 left-0 w-2 h-full bg-[#FFC232]"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-3">Total Closing</p>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white leading-none tracking-tighter">{{ number_format($overview['total_closings']) }}</h3>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-2.5 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-[#FFC232] rounded-full shadow-lg" style="width: {{ $overview['conversion_rate'] }}%"></div>
                            </div>
                            <span class="text-sm font-black text-[#FFC232] whitespace-nowrap">{{ $overview['conversion_rate'] }}% Rate</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl p-8 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity text-indigo-500">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                    </div>
                    <div class="absolute top-0 left-0 w-2 h-full bg-indigo-600"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] mb-3">Revenue Realization</p>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter">Rp {{ number_format($overview['total_revenue'], 0, ',', '.') }}</h3>
                        <p class="mt-6 text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 inline-block px-3 py-1.5 rounded-full">Omset closing valid</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                {{-- Channel Comparison --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-8 border-b border-gray-50 dark:border-gray-700">
                            <h4 class="font-black text-gray-900 dark:text-white uppercase tracking-tight">Channel Comparison</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Online vs Offline Intake</p>
                        </div>
                        <div class="p-8 space-y-10">
                            @foreach($channelStats as $stat)
                            <div>
                                <div class="flex justify-between items-end mb-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full {{ $stat['channel'] == 'ONLINE' ? 'bg-indigo-500' : 'bg-[#FFC232]' }}"></span>
                                            <span class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider">{{ $stat['channel'] }}</span>
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest tracking-tighter">Rev: Rp {{ number_format($stat['revenue'], 0, ',', '.') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-black text-gray-900 dark:text-white leading-none">{{ $stat['leads'] }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase ml-1 block">Leads</span>
                                    </div>
                                </div>
                                <div class="h-4 w-full bg-gray-50 dark:bg-gray-700 rounded-full overflow-hidden flex shadow-inner">
                                    <div class="{{ $stat['channel'] == 'ONLINE' ? 'bg-indigo-500 shadow-indigo-200' : 'bg-[#FFC232] shadow-yellow-200' }} h-full transition-all duration-1000" 
                                         style="width: {{ $overview['total_leads'] > 0 ? ($stat['leads'] / $overview['total_leads'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                            @endforeach

                            @if(count($channelStats) == 0)
                            <div class="text-center py-8 text-gray-400 italic text-sm">Belum ada data channel.</div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Tips/Info --}}
                    <div class="bg-indigo-600 rounded-[2rem] shadow-xl p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20">
                            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
                        </div>
                        <h4 class="font-black text-lg uppercase leading-tight mb-4 relative z-10">Insights Performa</h4>
                        <p class="text-xs font-bold leading-relaxed opacity-90 relative z-10">
                            Data ditampilkan berdasarkan intake (leads masuk) pada periode filter. <br><br>
                            Revenue dihitung dari SPK yang dibuat oleh masing-masing CS dalam periode tersebut.
                        </p>
                    </div>
                </div>

                {{-- CS KPI Leaderboard --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-8 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                            <div>
                                <h4 class="font-black text-gray-900 dark:text-white uppercase tracking-tight">CS KPI Leaderboard</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Ranking performa individu CS</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-200/20 dark:bg-gray-700/20">
                                        <th class="py-4 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">CS Nama</th>
                                        <th class="py-4 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Intake</th>
                                        <th class="py-4 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Closing</th>
                                        <th class="py-4 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">CR (%)</th>
                                        <th class="py-4 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    @forelse($csKpis as $kpi)
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="py-6 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                                                    <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">{{ substr($kpi['cs_name'], 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="font-black text-gray-900 dark:text-white text-sm">{{ $kpi['cs_name'] }}</div>
                                                    <div class="flex gap-2 mt-1">
                                                        <span class="text-[8px] font-black px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 rounded-full uppercase">ON: {{ $kpi['online_leads'] }}</span>
                                                        <span class="text-[8px] font-black px-2 py-0.5 bg-orange-50 dark:bg-orange-900/40 text-orange-600 dark:text-orange-300 rounded-full uppercase">OFF: {{ $kpi['offline_leads'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center font-black text-gray-700 dark:text-gray-300">{{ number_format($kpi['total_leads']) }}</td>
                                        <td class="py-6 px-6 text-center font-black text-green-600">{{ number_format($kpi['closings']) }}</td>
                                        <td class="py-6 px-6 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-sm font-black text-gray-900 dark:text-white">{{ $kpi['conversion_rate'] }}%</span>
                                                <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full mt-2 overflow-hidden shadow-inner">
                                                    <div class="h-full bg-[#22AF85] rounded-full shadow-lg" style="width: {{ $kpi['conversion_rate'] }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-right">
                                            <span class="px-4 py-2 bg-gray-100 dark:bg-gray-900 rounded-xl font-black text-gray-900 dark:text-teal-400 text-sm">
                                                Rp {{ number_format($kpi['revenue'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-gray-400 italic text-sm">Data performa belum tersedia untuk periode ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
