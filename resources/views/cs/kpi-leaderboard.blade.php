<x-app-layout>
    @push('styles')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        
        <style>
            .leaderboard-font-title {
                font-family: 'Outfit', sans-serif;
            }
            .leaderboard-font-body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            
            /* Glassmorphism utility */
            .glass-card {
                background: rgba(15, 23, 42, 0.45);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(51, 65, 85, 0.5);
            }
            
            /* Glow effects */
            .gold-glow {
                box-shadow: 0 0 25px rgba(234, 179, 8, 0.15);
                border-color: rgba(234, 179, 8, 0.4) !important;
            }
            .teal-glow {
                box-shadow: 0 0 25px rgba(20, 184, 166, 0.1);
            }
            
            /* Animated rank crown */
            @keyframes bob {
                0%, 100% { transform: translateY(0) rotate(-5deg); }
                50% { transform: translateY(-4px) rotate(5deg); }
            }
            .animate-bob {
                animation: bob 3s ease-in-out infinite;
            }

            /* Sorting indicators */
            .sort-btn::after {
                content: ' ↕';
                font-size: 0.8em;
                opacity: 0.5;
            }
            .sort-btn.active-desc::after {
                content: ' ↓';
                opacity: 1;
                color: #14b8a6;
            }
            .sort-btn.active-asc::after {
                content: ' ↑';
                opacity: 1;
                color: #14b8a6;
            }
        </style>
    @endpush

    <div class="min-h-screen bg-slate-950 text-slate-100 leaderboard-font-body py-8 px-4 sm:px-6 lg:px-8" x-data="kpiLeaderboard">
        <div class="max-w-7xl mx-auto space-y-8">
            
            {{-- Navigation and Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 text-[9px] font-black tracking-widest text-teal-400 bg-teal-500/10 rounded-lg border border-teal-500/20 uppercase">KPI Leaderboard</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-teal-400 via-teal-300 to-indigo-400 leaderboard-font-title mt-2">
                        CS Performance & Revenue Leaderboard
                    </h1>
                    <p class="text-slate-400 text-xs mt-1.5 font-medium max-w-2xl leading-relaxed">
                        Data performa operasional Customer Service (CS) real-time yang dihitung otomatis berdasarkan verifikasi invoice serta distribusi lead per-channel.
                    </p>
                </div>
                
                {{-- Date Filter Controls --}}
                <div class="flex flex-wrap items-center gap-3 bg-slate-900/60 p-3 rounded-[2rem] border border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <span class="text-[8px] uppercase font-black tracking-widest text-slate-500 absolute -top-2 left-2.5 bg-slate-950 px-1.5 rounded">Mulai</span>
                            <input type="date" x-model="startDate" @change="fetchData()" class="bg-slate-900 border-slate-800 rounded-xl text-xs font-bold text-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2 px-3">
                        </div>
                        <div class="relative">
                            <span class="text-[8px] uppercase font-black tracking-widest text-slate-500 absolute -top-2 left-2.5 bg-slate-950 px-1.5 rounded">Selesai</span>
                            <input type="date" x-model="endDate" @change="fetchData()" class="bg-slate-900 border-slate-800 rounded-xl text-xs font-bold text-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2 px-3">
                        </div>
                    </div>
                    <a href="{{ route('cs.forecasting.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition-all border border-slate-700 flex items-center gap-1.5 hover:text-white">
                        <span>📊 Forecasting</span>
                    </a>
                </div>
            </div>

            {{-- Stat KPI Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                
                {{-- Card 1: Top Performer (Champion by Closings) --}}
                <div class="glass-card rounded-[2.5rem] p-6 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:translate-y-[-4px]"
                     :class="topPerformer ? 'gold-glow' : ''">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-yellow-500/5 rounded-full blur-2xl"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500/20 to-yellow-400/20 border border-yellow-500/30 flex items-center justify-center relative flex-shrink-0">
                            <span class="text-2xl">👑</span>
                            <div class="absolute -top-3 right-[-3px] text-base animate-bob">🥇</div>
                        </div>
                        <div>
                            <div class="text-[9px] text-amber-400 font-black uppercase tracking-widest">Top CS Performer</div>
                            <div class="text-lg font-bold text-white leaderboard-font-title mt-1" x-text="topPerformer ? topPerformer.cs_name : 'No Data'">-</div>
                            <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1.5">
                                <span class="font-bold text-teal-400" x-text="topPerformer ? topPerformer.closings + ' Closing' : '-'">-</span>
                                <span class="text-slate-600">•</span>
                                <span class="font-semibold text-emerald-400" x-text="topPerformer ? formatCurrency(topPerformer.revenue) : 'Rp 0'">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Total CS Revenue Generated --}}
                <div class="glass-card rounded-[2.5rem] p-6 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:translate-y-[-4px] teal-glow">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-teal-500/5 rounded-full blur-2xl"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-teal-500/20 to-emerald-500/20 border border-teal-500/30 flex items-center justify-center text-teal-400 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <div class="text-[9px] text-teal-400 font-black uppercase tracking-widest">Total Invoice Revenue</div>
                            <div class="text-xl font-bold text-white leaderboard-font-title mt-1" x-text="formatCurrency(totalRevenue)">Rp 0</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium">Verified from SPKs</div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Total Closing (Converted) --}}
                <div class="glass-card rounded-[2.5rem] p-6 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:translate-y-[-4px]">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-500/5 rounded-full blur-2xl"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <div class="text-[9px] text-indigo-400 font-black uppercase tracking-widest">Total Closing (Converted)</div>
                            <div class="text-xl font-bold text-white leaderboard-font-title mt-1" x-text="totalClosing + ' Closing'">0 Closing</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium flex items-center gap-1">
                                <span class="text-emerald-400 font-bold" x-text="list.reduce((acc, curr) => acc + parseInt(curr.closing_direct), 0) + ' Dir'">0 Dir</span>
                                <span>/</span>
                                <span class="text-amber-500 font-bold" x-text="list.reduce((acc, curr) => acc + parseInt(curr.closing_via_followup), 0) + ' FU'">0 FU</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Total Sepatu Diterima (Status selain SPK PENDING dan Batal) --}}
                <div class="glass-card rounded-[2.5rem] p-6 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:translate-y-[-4px] border-emerald-500/20">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/20 border border-emerald-500/20 flex items-center justify-center text-emerald-400 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <div>
                            <div class="text-[9px] text-emerald-400 font-black uppercase tracking-widest">Total Sepatu Diterima</div>
                            <div class="text-xl font-bold text-white leaderboard-font-title mt-1" x-text="totalDiterima + ' Pasang'">0 Pasang</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium flex items-center gap-1">
                                <span class="text-teal-400 font-bold" x-text="totalDiterimaOnline + ' OL'">0 OL</span>
                                <span>/</span>
                                <span class="text-indigo-400 font-bold" x-text="totalDiterimaOffline + ' OFF'">0 OFF</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Total SPK Pending --}}
                <div class="glass-card rounded-[2.5rem] p-6 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:translate-y-[-4px] border-amber-500/20">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/5 rounded-full blur-2xl"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500/20 to-orange-500/20 border border-amber-500/20 flex items-center justify-center text-amber-500 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <div class="text-[9px] text-amber-500 font-black uppercase tracking-widest">Total SPK Pending</div>
                            <div class="text-xl font-bold text-white leaderboard-font-title mt-1" x-text="totalSpkPending + ' Pasang'">0 Pasang</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium">Belum di-receive workshop</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Leaderboard Table Section --}}
            <div class="rounded-[2.5rem] border border-slate-800 bg-slate-900/20 backdrop-blur-md overflow-hidden shadow-2xl">
                
                {{-- Table Header Info --}}
                <div class="p-6 border-b border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-950/20">
                    <h3 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-3 leaderboard-font-title">
                        <span class="w-1.5 h-6 bg-gradient-to-b from-teal-400 to-indigo-500 rounded-full"></span>
                        Rangking Efisiensi & Hasil CS
                    </h3>
                    
                    {{-- Legend / Short Info --}}
                    <div class="flex items-center gap-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-teal-500 rounded"></span> Online</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-indigo-500 rounded"></span> Offline</span>
                    </div>
                </div>

                {{-- Table Container --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-300">
                        <thead class="text-[10px] text-slate-500 uppercase tracking-widest bg-slate-950/40 border-b border-slate-800/80">
                            <tr>
                                <th class="px-6 py-4.5 text-center font-bold w-16">Rank</th>
                                <th class="px-6 py-4.5 font-bold">CS Agent</th>
                                
                                {{-- Interactive Sort Headers --}}
                                <th class="px-6 py-4.5 text-center font-bold cursor-pointer hover:bg-slate-900/30 transition-colors sort-btn"
                                    :class="sortByField === 'incoming_items' ? (sortAscending ? 'active-asc' : 'active-desc') : ''"
                                    @click="sortBy('incoming_items')">
                                    Intake (Sepatu Masuk)
                                </th>
                                <th class="px-6 py-4.5 text-center font-bold cursor-pointer hover:bg-slate-900/30 transition-colors sort-btn"
                                    :class="sortByField === 'closings' ? (sortAscending ? 'active-asc' : 'active-desc') : ''"
                                    @click="sortBy('closings')">
                                    Closing (Converted)
                                </th>
                                <th class="px-6 py-4.5 text-center font-bold cursor-pointer hover:bg-slate-900/30 transition-colors sort-btn"
                                    :class="sortByField === 'sepatu_diterima' ? (sortAscending ? 'active-asc' : 'active-desc') : ''"
                                    @click="sortBy('sepatu_diterima')">
                                    Sepatu Diterima
                                </th>
                                <th class="px-6 py-4.5 text-center font-bold cursor-pointer hover:bg-slate-900/30 transition-colors sort-btn"
                                    :class="sortByField === 'sepatu_spk_pending' ? (sortAscending ? 'active-asc' : 'active-desc') : ''"
                                    @click="sortBy('sepatu_spk_pending')">
                                    SPK Pending
                                </th>
                                <th class="px-6 py-4.5 text-right font-bold cursor-pointer hover:bg-slate-900/30 transition-colors sort-btn"
                                    :class="sortByField === 'revenue' ? (sortAscending ? 'active-asc' : 'active-desc') : ''"
                                    @click="sortBy('revenue')">
                                    Revenue
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            
                            {{-- Skeleton Loading Placeholder Rows --}}
                            <template x-if="isLoading">
                                <template x-for="i in 5">
                                    <tr class="animate-pulse">
                                        <td class="px-6 py-5.5 text-center">
                                            <div class="w-7 h-7 bg-slate-800 rounded-lg mx-auto"></div>
                                        </td>
                                        <td class="px-6 py-5.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-slate-800"></div>
                                                <div class="space-y-1.5">
                                                    <div class="h-3.5 bg-slate-800 rounded w-24"></div>
                                                    <div class="h-2 bg-slate-800 rounded w-16"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5.5 text-center">
                                            <div class="h-4 bg-slate-800 rounded w-12 mx-auto"></div>
                                            <div class="h-2.5 bg-slate-800 rounded w-16 mx-auto mt-2"></div>
                                        </td>
                                        <td class="px-6 py-5.5 text-center">
                                            <div class="h-4 bg-slate-800 rounded w-10 mx-auto"></div>
                                            <div class="h-2.5 bg-slate-800 rounded w-14 mx-auto mt-2"></div>
                                        </td>
                                        <td class="px-6 py-5.5 text-center">
                                            <div class="h-4 bg-slate-800 rounded w-12 mx-auto"></div>
                                            <div class="h-2.5 bg-slate-800 rounded w-16 mx-auto mt-2"></div>
                                        </td>
                                        <td class="px-6 py-5.5 text-center">
                                            <div class="h-4 bg-slate-800 rounded w-10 mx-auto"></div>
                                        </td>
                                        <td class="px-6 py-5.5 text-right">
                                            <div class="h-4 bg-slate-800 rounded w-20 ml-auto"></div>
                                        </td>
                                    </tr>
                                </template>
                            </template>

                            {{-- Dynamic Hydrated Rows --}}
                            <template x-if="!isLoading && list.length > 0">
                                <template x-for="(item, index) in list" :key="item.cs_name">
                                    <tr class="hover:bg-slate-800/10 transition-all border-l-4"
                                        :class="index === 0 ? 'border-yellow-500 bg-yellow-500/[0.01]' : (index === 1 ? 'border-slate-400 bg-slate-400/[0.005]' : (index === 2 ? 'border-amber-700' : 'border-transparent'))">
                                        
                                        {{-- Rank Indicator --}}
                                        <td class="px-6 py-4.5 text-center">
                                            <span x-show="index === 0" class="text-xl">🥇</span>
                                            <span x-show="index === 1" class="text-xl">🥈</span>
                                            <span x-show="index === 2" class="text-xl">🥉</span>
                                            <span x-show="index > 2" class="inline-flex items-center justify-center w-7 h-7 bg-slate-900 rounded-lg text-slate-400 text-xs font-bold border border-slate-800" x-text="index + 1"></span>
                                        </td>
                                        
                                        {{-- CS Profile Name --}}
                                        <td class="px-6 py-4.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black text-white shadow-md relative"
                                                     :class="index === 0 ? 'bg-gradient-to-tr from-yellow-500 to-amber-400' : (index === 1 ? 'bg-gradient-to-tr from-slate-400 to-slate-300' : (index === 2 ? 'bg-gradient-to-tr from-amber-700 to-amber-600' : 'bg-slate-800'))">
                                                    <span x-text="item.cs_avatar_initial"></span>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-white leading-tight" x-text="item.cs_name"></div>
                                                    <div class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5" x-text="'Total ' + item.total_leads + ' Leads'">0 Leads</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Intake Metrics (Online / Offline split) --}}
                                        <td class="px-6 py-4.5 text-center">
                                            <div class="text-white font-extrabold text-sm" x-text="item.incoming_items + ' Psg'"></div>
                                            <div class="text-[9px] text-slate-500 mt-1 flex items-center justify-center gap-1">
                                                <span class="font-bold text-teal-400" x-text="item.incoming_items_online + ' OL'"></span>
                                                <span>•</span>
                                                <span class="font-bold text-indigo-400" x-text="item.incoming_items_offline + ' OFF'"></span>
                                            </div>
                                        </td>

                                        {{-- Converted Closings (Direct vs Follow Up split) --}}
                                        <td class="px-6 py-4.5 text-center">
                                            <div class="text-white font-bold text-sm" x-text="item.closings + ' Closing'"></div>
                                            <div class="text-[9px] text-slate-500 mt-1 flex items-center justify-center gap-1">
                                                <span class="font-semibold text-emerald-400" x-text="'Dir: ' + item.closing_direct"></span>
                                                <span>/</span>
                                                <span class="font-semibold text-amber-500" x-text="'FU: ' + item.closing_via_followup"></span>
                                            </div>
                                        </td>

                                        {{-- Sepatu Diterima (Online vs Offline split) --}}
                                        <td class="px-6 py-4.5 text-center">
                                            <div class="text-white font-extrabold text-sm" x-text="item.sepatu_diterima + ' Psg'"></div>
                                            <div class="text-[9px] text-slate-500 mt-1 flex items-center justify-center gap-1">
                                                <span class="font-bold text-teal-400" x-text="item.sepatu_diterima_online + ' OL'"></span>
                                                <span>•</span>
                                                <span class="font-bold text-indigo-400" x-text="item.sepatu_diterima_offline + ' OFF'"></span>
                                            </div>
                                        </td>

                                        {{-- SPK Pending --}}
                                        <td class="px-6 py-4.5 text-center">
                                            <div class="text-amber-500 font-extrabold text-sm" x-text="item.sepatu_spk_pending + ' Psg'"></div>
                                        </td>

                                        {{-- Invoice base Revenue --}}
                                        <td class="px-6 py-4.5 text-right">
                                            <div class="text-emerald-400 font-black text-sm" x-text="formatCurrency(item.revenue)">Rp 0</div>
                                            <div class="text-[9px] text-slate-500 mt-1 font-bold" x-text="'AIO: ' + item.aio + ' Psg/Order'">AIO: 0</div>
                                        </td>

                                    </tr>
                                </template>
                            </template>

                            {{-- Empty State Row --}}
                            <template x-if="!isLoading && list.length === 0">
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <span class="text-4xl">📭</span>
                                            <div class="font-bold text-sm text-slate-400">Tidak ada data untuk periode terpilih</div>
                                            <p class="text-xs text-slate-600 max-w-xs">Silakan sesuaikan tanggal mulai dan selesai di filter bagian atas halaman.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
    
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('kpiLeaderboard', () => ({
                    isLoading: true,
                    startDate: '{{ now()->startOfMonth()->format("Y-m-d") }}',
                    endDate: '{{ now()->format("Y-m-d") }}',
                    list: [],
                    topPerformer: null,
                    totalRevenue: 0,
                    totalClosing: 0,
                    totalDiterima: 0,
                    totalDiterimaOnline: 0,
                    totalDiterimaOffline: 0,
                    totalSpkPending: 0,
                    sortByField: 'closings',
                    sortAscending: false,

                    init() {
                        this.fetchData();
                    },

                    fetchData() {
                        this.isLoading = true;
                        let url = `/cs/kpi-leaderboard/api-data?start_date=${this.startDate}&end_date=${this.endDate}`;
                        fetch(url)
                            .then(res => res.json())
                            .then(res => {
                                if (res.status === 'success') {
                                    this.list = res.data;
                                    this.calculateSummary();
                                } else {
                                    console.error('API returned unsuccessful status');
                                }
                                this.isLoading = false;
                            })
                            .catch(err => {
                                console.error('Error fetching data:', err);
                                this.isLoading = false;
                            });
                    },

                    calculateSummary() {
                        if (this.list.length === 0) {
                            this.topPerformer = null;
                            this.totalRevenue = 0;
                            this.totalClosing = 0;
                            this.totalDiterima = 0;
                            this.totalDiterimaOnline = 0;
                            this.totalDiterimaOffline = 0;
                            this.totalSpkPending = 0;
                            return;
                        }

                        // The list from backend is sorted by closings desc by default
                        this.topPerformer = this.list[0];

                        this.totalRevenue = this.list.reduce((acc, curr) => acc + parseFloat(curr.revenue), 0);
                        this.totalClosing = this.list.reduce((acc, curr) => acc + parseInt(curr.closings), 0);
                        this.totalDiterima = this.list.reduce((acc, curr) => acc + parseInt(curr.sepatu_diterima), 0);
                        this.totalDiterimaOnline = this.list.reduce((acc, curr) => acc + parseInt(curr.sepatu_diterima_online), 0);
                        this.totalDiterimaOffline = this.list.reduce((acc, curr) => acc + parseInt(curr.sepatu_diterima_offline), 0);
                        this.totalSpkPending = this.list.reduce((acc, curr) => acc + parseInt(curr.sepatu_spk_pending), 0);
                    },

                    sortBy(field) {
                        if (this.sortByField === field) {
                            this.sortAscending = !this.sortAscending;
                        } else {
                            this.sortByField = field;
                            this.sortAscending = false;
                        }

                        this.list.sort((a, b) => {
                            let valA = a[field];
                            let valB = b[field];
                            
                            // Handling null/undefined values
                            if (valA === null || valA === undefined) valA = 0;
                            if (valB === null || valB === undefined) valB = 0;
                            
                            if (typeof valA === 'string') {
                                return this.sortAscending 
                                    ? valA.localeCompare(valB) 
                                    : valB.localeCompare(valA);
                            } else {
                                return this.sortAscending 
                                    ? valA - valB 
                                    : valB - valA;
                            }
                        });
                    },

                    formatCurrency(val) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(val);
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>
