<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            
            {{-- Premium Header Section --}}
            <section class="relative overflow-hidden bg-gradient-to-br from-teal-600 via-teal-700 to-orange-600 rounded-3xl shadow-2xl">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-orange-500/20 rounded-full blur-3xl"></div>
                
                <div class="relative px-8 py-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white/90 text-xs font-bold mb-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                Live Analytics
                            </div>
                            <span id="cx-live-indicator" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-sm rounded-full ml-2">
                                <span class="text-[10px] font-bold text-white/70 cx-live-time"></span>
                            </span>
                            <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                                CX Dashboard
                            </h1>
                            <p class="text-teal-100 text-lg font-medium">
                                Issue Tracking & Customer Satisfaction Analytics
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            {{-- Date Filter --}}
                            <div class="flex flex-col gap-2">
                                <form id="date-filter-form" action="{{ route('cx.dashboard') }}" method="GET" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 shadow-lg">
                                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <input type="date" name="start_date" id="start_date" value="{{ $filterStartDate }}" 
                                        class="bg-transparent border-none text-white text-xs focus:ring-0 cursor-pointer font-bold p-0"
                                        onchange="this.form.submit()">
                                    <span class="text-white/40 text-[10px]">—</span>
                                    <input type="date" name="end_date" id="end_date" value="{{ $filterEndDate }}" 
                                        class="bg-transparent border-none text-white text-xs focus:ring-0 cursor-pointer font-bold p-0"
                                        onchange="this.form.submit()">
                                </form>
                                <div class="flex items-center justify-end gap-1.5 px-1">
                                    <button type="button" onclick="document.getElementById('start_date').value = '{{ now()->format('Y-m-d') }}'; document.getElementById('end_date').value = '{{ now()->format('Y-m-d') }}'; document.getElementById('date-filter-form').submit()" 
                                        class="px-2 py-0.5 text-[9px] font-black uppercase tracking-tighter bg-white/10 hover:bg-white text-white hover:text-teal-700 rounded-lg transition-all border border-white/10">
                                        Hari Ini
                                    </button>
                                    <button type="button" onclick="document.getElementById('start_date').value = '{{ now()->subDay()->format('Y-m-d') }}'; document.getElementById('end_date').value = '{{ now()->subDay()->format('Y-m-d') }}'; document.getElementById('date-filter-form').submit()" 
                                        class="px-2 py-0.5 text-[9px] font-black uppercase tracking-tighter bg-white/10 hover:bg-white text-white hover:text-teal-700 rounded-lg transition-all border border-white/10">
                                        Kemarin
                                    </button>
                                    <button type="button" onclick="document.getElementById('start_date').value = '{{ now()->startOfWeek()->format('Y-m-d') }}'; document.getElementById('end_date').value = '{{ now()->endOfWeek()->format('Y-m-d') }}'; document.getElementById('date-filter-form').submit()" 
                                        class="px-2 py-0.5 text-[9px] font-black uppercase tracking-tighter bg-white/10 hover:bg-white text-white hover:text-teal-700 rounded-lg transition-all border border-white/10">
                                        Minggu Ini
                                    </button>
                                    <button type="button" onclick="document.getElementById('start_date').value = '{{ now()->startOfMonth()->format('Y-m-d') }}'; document.getElementById('end_date').value = '{{ now()->endOfMonth()->format('Y-m-d') }}'; document.getElementById('date-filter-form').submit()" 
                                        class="px-2 py-0.5 text-[9px] font-black uppercase tracking-tighter bg-white/10 hover:bg-white text-white hover:text-teal-700 rounded-lg transition-all border border-white/10">
                                        Bulan Ini
                                    </button>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <a href="{{ route('cx.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-teal-700 rounded-xl font-bold hover:bg-teal-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Follow Up List
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- KPI Metrics Section --}}
            <section>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                    {{-- Total Issues --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div x-data="{ isOpen: false }" class="relative">
                                <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Maksud</div>
                                        <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium text-left">Total seluruh kendala atau tiket bantuan yang masuk ke sistem CX.</div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sumber Data</div>
                                        <div class="text-[11px] text-gray-500 italic leading-snug text-left">Tabel cx_issues (Semua Baris).</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cx-stat-total" class="text-3xl font-black text-gray-800 mb-1">{{ $totalIssues }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Issues</div>
                    </div>

                    {{-- Open --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100 hover:border-red-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div x-data="{ isOpen: false }" class="relative">
                                <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-red-200 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-red-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-red-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Maksud</div>
                                        <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium text-left">Jumlah isu yang baru masuk dan belum direspon oleh tim CX.</div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sumber Data</div>
                                        <div class="text-[11px] text-gray-500 italic leading-snug text-left">cx_issues dengan status 'OPEN'.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cx-stat-open" class="text-3xl font-black text-red-600 mb-1">{{ $openIssues }}</div>
                        <div class="text-xs font-bold text-red-500 uppercase tracking-wider">Open</div>
                    </div>

                    {{-- In Progress --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-orange-100 hover:border-orange-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div x-data="{ isOpen: false }" class="relative">
                                <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-orange-200 hover:text-orange-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-orange-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-1">Maksud</div>
                                        <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium text-left">Isu yang sedang dalam tahap penanganan atau follow-up oleh tim CX.</div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sumber Data</div>
                                        <div class="text-[11px] text-gray-500 italic leading-snug text-left">cx_issues dengan status 'IN_PROGRESS'.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cx-stat-inprogress" class="text-3xl font-black text-orange-600 mb-1">{{ $inProgressIssues }}</div>
                        <div class="text-xs font-bold text-orange-500 uppercase tracking-wider">In Progress</div>
                    </div>

                    {{-- Resolved --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-teal-100 hover:border-teal-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div x-data="{ isOpen: false }" class="relative">
                                <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-teal-200 hover:text-teal-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-teal-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-teal-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="text-[10px] font-black text-teal-400 uppercase tracking-widest mb-1">Maksud</div>
                                        <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium text-left">
                                            Isu yang sudah selesai ditangani. Terbagi menjadi:<br>
                                            <span class="text-teal-600 font-bold">• Tambah Jasa:</span> Menghasilkan pendapatan tambahan.<br>
                                            <span class="text-gray-500 font-bold">• Lanjut:</span> Selesai tanpa tambahan biaya.
                                        </div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sumber Data</div>
                                        <div class="text-[11px] text-gray-500 italic leading-snug text-left">cx_issues dengan status 'RESOLVED'.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cx-stat-resolved" class="text-3xl font-black text-teal-600 mb-1">{{ $resolvedIssues }}</div>
                        <div class="text-xs font-bold text-teal-500 uppercase tracking-wider">Resolved</div>
                        
                        {{-- Breakdown --}}
                        <div class="mt-4 pt-4 border-t border-teal-50 flex items-center gap-2">
                            <div class="flex-1 flex items-center justify-center gap-1.5 py-1.5 bg-teal-50 rounded-lg text-teal-600 text-[9px] font-black uppercase tracking-tight whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $resolvedWithUpsell }} Upsell</span>
                            </div>
                            <div class="flex-1 flex items-center justify-center gap-1.5 py-1.5 bg-gray-50 rounded-lg text-gray-400 text-[9px] font-black uppercase tracking-tight whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                <span>{{ $resolvedNoUpsell }} Lanjut</span>
                            </div>
                        </div>
                    </div>

                    {{-- Cancelled --}}
                    <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-rose-100 hover:border-rose-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-rose-100 to-rose-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div x-data="{ isOpen: false }" class="relative">
                                <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-rose-200 hover:text-rose-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-rose-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-rose-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">Maksud</div>
                                        <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium text-left">Order yang akhirnya dibatalkan (Batal) sebagai hasil tindak lanjut CX.</div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sumber Data</div>
                                        <div class="text-[11px] text-gray-500 italic leading-snug text-left">cx_issues -> resolved, dengan status SPK 'BATAL'.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cx-stat-cancelled" class="text-3xl font-black text-rose-600 mb-1">{{ $cancelledIssues }}</div>
                        <div class="text-xs font-bold text-rose-500 uppercase tracking-wider">Cancelled</div>
                    </div>

                    {{-- Avg Response --}}
                    <div class="group bg-white rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div x-data="{ isOpen: false }" class="relative">
                                <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                                    <div class="relative text-left">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Maksud</div>
                                        <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium">Rata-rata waktu penyelesaian isu.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cx-stat-response" class="text-2xl font-black text-gray-800 mb-0.5 tracking-tighter">{{ $avgResponseTime }}h</div>
                        <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Avg Time</div>
                    </div>

                    {{-- Resolution Rate --}}
                    <div class="group bg-teal-600 rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div x-data="{ isOpen: false }" class="relative">
                                <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-teal-200 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-teal-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-teal-100 rotate-45"></div>
                                    <div class="relative text-left">
                                        <div class="text-[10px] font-black text-teal-400 uppercase tracking-widest mb-1">Success Rate</div>
                                        <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium">Persentase keberhasilan isu.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cx-stat-resolution" class="text-2xl font-black text-white mb-0.5 tracking-tighter">{{ $resolutionRate }}%</div>
                        <div class="text-[9px] font-bold text-teal-100 uppercase tracking-widest">Res Rate</div>
                    </div>
                </div>
            </section>

            {{-- Section Header: Financial --}}
            <div class="flex items-center gap-3 px-2">
                <div class="w-1.5 h-8 bg-teal-600 rounded-full"></div>
                <div>
                    <h2 class="text-xl font-black text-gray-800 tracking-tight">Financial Analytics</h2>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Revenue & Upsell Performance</p>
                </div>
            </div>

            {{-- Financial Overview Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Tambah Jasa Summary --}}
                <div class="group bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-white font-black tracking-tight uppercase text-sm">Summary Tambah Jasa</h3>
                                <div x-data="{ isOpen: false }" class="relative">
                                    <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-teal-200 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-teal-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-teal-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="text-[10px] font-black text-teal-400 uppercase tracking-widest mb-1">Maksud</div>
                                            <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium">Total nominal rupiah dari layanan tambahan (**Tambah Jasa**) periode terpilih.</div>
                                            <div class="text-[11px] text-teal-600 bg-teal-50 p-2 rounded-lg border border-teal-100 mb-3 italic">
                                                Dihitung berdasarkan **Tanggal Selesai (Closing)** kendala CX di periode ini, terlepas dari kapan tiketnya dibuat.
                                            </div>
                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sumber Data</div>
                                            <div class="text-[11px] text-gray-500 italic leading-snug">Penjumlahan biaya dari tabel work_order_services (CX-Driven).</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[9px] font-black text-teal-100 uppercase tracking-widest bg-white/10 px-2 py-1 rounded-lg border border-white/10">Revenue Stream</span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-end justify-between mb-8 gap-4">
                            <div class="flex-1">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 font-inter">Total Nominal</div>
                                <div class="text-4xl font-black text-gray-800 tracking-tighter font-inter whitespace-nowrap">Rp{{ number_format($totalTambahJasaNominal, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex gap-2">
                                <div class="text-right">
                                    <div class="text-[9px] font-black text-teal-500 uppercase tracking-widest mb-1">Volume</div>
                                    <div class="px-2 py-1 bg-teal-50 text-teal-600 rounded-lg text-[11px] font-black border border-teal-100 whitespace-nowrap">
                                        {{ $totalSpkTambahJasa }} SPK
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[9px] font-black text-teal-600 uppercase tracking-widest mb-1">ARPU</div>
                                    <div class="px-2 py-1 bg-teal-600 text-white rounded-lg text-[11px] font-black shadow-sm whitespace-nowrap">
                                        Rp{{ number_format($arpuTambahJasa, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2 flex justify-between">
                                <span>Top 5 Services Taken</span>
                                <span>Nominal</span>
                            </div>
                            @forelse($tambahJasaItems as $item)
                            <div class="flex items-center justify-between group/item">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 font-black text-xs border border-teal-100 group-hover/item:scale-110 transition-transform">
                                        {{ $item->count }}
                                    </div>
                                    <div>
                                        <div class="text-[11px] font-black text-gray-700 tracking-tight leading-none mb-1">{{ $item->category_name }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium line-clamp-1 italic">
                                            {{ $item->custom_service_name ?: ($item->service->name ?? 'Custom Service') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-black text-gray-800 tracking-tight">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</div>
                                    <div class="text-[8px] text-teal-500 font-black uppercase tracking-tighter">Contribution</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-400 text-xs font-medium italic bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                Belum ada data tambahan jasa dalam periode ini
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- OTO Summary --}}
                <div class="group bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-white font-black tracking-tight uppercase text-sm">Summary OTO Revenue</h3>
                                <div x-data="{ isOpen: false }" class="relative">
                                    <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-orange-200 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-64 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-orange-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-1">Maksud</div>
                                            <div class="text-[12px] text-gray-700 leading-relaxed mb-3 font-medium">Total nominal rupiah dari penawaran sistem (**OTO**) yang berhasil diterima konsumen melalui tim CX.</div>
                                            <div class="text-[11px] text-orange-600 bg-orange-50 p-2 rounded-lg border border-orange-100 mb-3 italic">
                                                Dihitung berdasarkan **Tanggal Konfirmasi Pelanggan (Accepted)** di periode ini.
                                            </div>
                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sumber Data</div>
                                            <div class="text-[11px] text-gray-500 italic leading-snug">Data tabel otos dengan status 'ACCEPTED'.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[9px] font-black text-orange-100 uppercase tracking-widest bg-white/10 px-2 py-1 rounded-lg border border-white/10">Upsell Growth</span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-end justify-between mb-8 gap-4">
                            <div class="flex-1">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 font-inter">Total OTO Accepted</div>
                                <div class="text-4xl font-black text-gray-800 tracking-tighter font-inter whitespace-nowrap">Rp{{ number_format($totalOtoNominal, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex gap-2">
                                <div class="text-right">
                                    <div class="text-[9px] font-black text-orange-500 uppercase tracking-widest mb-1">Volume</div>
                                    <div class="px-2 py-1 bg-orange-50 text-orange-600 rounded-lg text-[11px] font-black border border-orange-100 whitespace-nowrap">
                                        {{ $totalSpkOto }} SPK
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[9px] font-black text-orange-600 uppercase tracking-widest mb-1">ARPU</div>
                                    <div class="px-2 py-1 bg-orange-600 text-white rounded-lg text-[11px] font-black shadow-sm whitespace-nowrap">
                                        Rp{{ number_format($arpuOto, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Top 5 OTO Packages</div>
                            @forelse($otoItems as $item)
                            <div class="flex items-center justify-between group/item">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 font-black text-xs border border-orange-100 group-hover/item:scale-110 transition-transform">
                                        {{ $item->count }}
                                    </div>
                                    <div class="text-[11px] font-black text-gray-700 tracking-tight">{{ $item->title }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-black text-gray-800 tracking-tight">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</div>
                                    <div class="text-[8px] text-orange-500 font-black uppercase tracking-tighter italic">Total Revenue</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-400 text-xs font-medium italic bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                Belum ada OTO yang diterima dalam periode ini
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section Header: Trends & Distribution --}}
            <div class="flex items-center gap-3 px-2">
                <div class="w-1.5 h-8 bg-orange-500 rounded-full"></div>
                <div>
                    <h2 class="text-xl font-black text-gray-800 tracking-tight">Growth Trends</h2>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Issue & Category Distribution</p>
                </div>
            </div>

            {{-- Charts Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Trend Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-orange-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div>
                                        <h3 class="text-lg font-black text-gray-800">Issue Trend Analysis</h3>
                                        <div class="flex items-center gap-2">
                                            <p class="text-xs text-gray-500 font-medium">Daily performance tracking</p>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Total Resolved: {{ $resolvedIssues }}</p>
                                        </div>
                                    </div>
                                    <div x-data="{ isOpen: false }" class="relative">
                                        <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-teal-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-72 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-teal-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                            <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-teal-100 rotate-45"></div>
                                            <div class="relative">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
                                                    <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Maksud</div>
                                                </div>
                                                <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Memantau naik-turunnya jumlah isu harian berdasarkan statusnya (Open, In Progress, Resolved).</div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                    <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                                </div>
                                                <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Log harian dari tabel cx_issues.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-white rounded-lg text-xs font-bold text-gray-600 shadow-sm border border-gray-200">
                                {{ \Carbon\Carbon::parse($filterStartDate)->format('d M') }} - {{ \Carbon\Carbon::parse($filterEndDate)->format('d M') }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $datasets = [
                                [
                                    'label' => 'Incoming Issues',
                                    'data' => $trendOpen,
                                    'borderColor' => '#f97316',
                                    'backgroundColor' => 'transparent',
                                    'fill' => false,
                                    'tension' => 0.4
                                ],
                                [
                                    'label' => 'Resolved Problems',
                                    'data' => $trendResolved,
                                    'borderColor' => '#14b8a6',
                                    'backgroundColor' => 'transparent',
                                    'fill' => false,
                                    'borderWidth' => 3,
                                    'tension' => 0.4
                                ]
                            ];
                        @endphp
                        <x-line-chart id="issueTrendChart" :labels="$trendLabels" :datasets="$datasets" />
                    </div>
                </div>

                {{-- Issue by Category --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2">
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">By Category</h3>
                                    <p class="text-xs text-gray-500 font-medium">Issue distribution</p>
                                </div>
                                <div x-data="{ isOpen: false }" class="relative">
                                    <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-orange-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-72 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-orange-100 left-0 mt-2 whitespace-normal text-left">
                                        <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                                <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                                            </div>
                                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Mengetahui jenis masalah yang paling sering terjadi (misal: kondis bawaan, salah kirim, dll).</div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                            </div>
                                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Kolom 'category' pada tabel cx_issues.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative min-h-[300px]">
                            <x-donut-chart 
                                id="categoryChart" 
                                :labels="$issuesByCategory->pluck('category')" 
                                :data="$issuesByCategory->pluck('count')" 
                                :colors="['#14b8a6', '#f97316', '#3b82f6', '#ef4444', '#8b5cf6', '#6b7280']" 
                                height="280" />
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none -translate-y-6">
                                <div class="text-center">
                                    <div class="text-4xl font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent">
                                        {{ $issuesByCategory->sum('count') }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            {{-- Analytics Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Issue by Source --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2">
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Issue Source</h3>
                                    <p class="text-xs text-gray-500 font-medium">Where issues originate</p>
                                </div>
                                <div x-data="{ isOpen: false }" class="relative">
                                    <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-teal-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-72 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-teal-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-teal-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
                                                <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Maksud</div>
                                            </div>
                                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Mengetahui dari departemen/area mana kendala paling sering muncul.</div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                            </div>
                                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Kolom 'source' pada tabel cx_issues.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($issuesBySource->count() > 0)
                            <x-bar-chart 
                                id="sourceChart" 
                                :labels="$issuesBySource->pluck('source')" 
                                :data="$issuesBySource->pluck('count')"
                                label="Issues Reported"
                                color="#14b8a6"
                            />
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">No source data available</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Top Resolvers --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2">
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Top Resolvers</h3>
                                    <p class="text-xs text-gray-500 font-medium">Team performance leaders</p>
                                </div>
                                <div x-data="{ isOpen: false }" class="relative">
                                    <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-orange-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-72 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-orange-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                                <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                                            </div>
                                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Data apresiasi untuk tim CX yang paling banyak menyelesaikan kendala pelanggan.</div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                            </div>
                                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Jumlah 'RESOLVED' tiket per user.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($topResolvers->count() > 0)
                            <div class="space-y-4">
                                @foreach($topResolvers as $index => $resolver)
                                    <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50/80 transition-all border border-transparent hover:border-gray-100 group">
                                        <div class="flex-shrink-0 relative">
                                            @if($index === 0)
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-yellow-400 to-amber-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($index === 1)
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-300 to-slate-500 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                                                    <span class="text-white font-black text-lg">2</span>
                                                </div>
                                            @elseif($index === 2)
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                                                    <span class="text-white font-black text-lg">3</span>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center group-hover:bg-teal-50 transition-colors">
                                                    <span class="text-gray-400 font-black group-hover:text-teal-600">#{{ $index + 1 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-black text-gray-800 tracking-tight">{{ $resolver->resolver->name ?? 'Unknown Resolver' }}</span>
                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $resolver->resolved_count }} RESOLVED</span>
                                            </div>
                                            <div class="w-full bg-gray-100/50 rounded-full h-2.5 overflow-hidden">
                                                <div class="h-full rounded-full bg-gradient-to-r from-teal-500 via-teal-600 to-orange-500 transition-all duration-1000 shadow-[0_0_8px_rgba(20,184,166,0.3)]" 
                                                     style="width: {{ min(($resolver->resolved_count / ($topResolvers->max('resolved_count') ?: 1)) * 100, 100) }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">No resolver data available</p>
                            </div>
                        @endif
                    </div>
                </div>

            </section>

            {{-- Section Header: Operations --}}
            <div class="flex items-center gap-3 px-2">
                <div class="w-1.5 h-8 bg-gray-400 rounded-full"></div>
                <div>
                    <h2 class="text-xl font-black text-gray-800 tracking-tight">Operations Log</h2>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Recent Activity & Task Management</p>
                </div>
            </div>

            {{-- Activity Section --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Issues --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2">
                                <div>
                                    <h3 class="text-lg font-black text-gray-800">Recent Activity</h3>
                                    <p class="text-xs text-gray-500 font-medium">Latest 15 issues</p>
                                </div>
                                <div x-data="{ isOpen: false }" class="relative">
                                    <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-teal-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-72 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-teal-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-teal-100 rotate-45"></div>
                                        <div class="relative">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
                                                <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Maksud</div>
                                            </div>
                                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Memantau aktivitas terbaru terkait isu yang masuk, termasuk status dan pelapor.</div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                            </div>
                                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Log aktivitas dari tabel cx_issues.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                             @forelse($recentIssues as $issue)
                             <div class="group flex gap-4 p-4 bg-white rounded-2xl hover:bg-gray-50/80 transition-all border border-gray-100 hover:border-teal-100 shadow-sm hover:shadow-md">
                                 <div class="flex-shrink-0 mt-1">
                                     <div class="w-3 h-3 rounded-full {{ $issue->status === 'OPEN' ? 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.4)]' : ($issue->status === 'IN_PROGRESS' ? 'bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]' : 'bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.4)]') }} ring-4 {{ $issue->status === 'OPEN' ? 'ring-red-50' : ($issue->status === 'IN_PROGRESS' ? 'ring-orange-50' : 'ring-teal-50') }}"></div>
                                 </div>
                                 <div class="flex-1 min-w-0">
                                     <div class="flex items-start justify-between gap-3 mb-1.5">
                                         <div class="font-black text-gray-800 text-sm tracking-tight group-hover:text-teal-700 transition-colors">
                                             {{ $issue->workOrder->spk_number ?? 'Unknown SPK' }}
                                         </div>
                                         <span class="flex-shrink-0 px-2.5 py-1 rounded-lg text-[9px] font-black tracking-widest uppercase border {{ $issue->status === 'OPEN' ? 'bg-red-50 text-red-600 border-red-100' : ($issue->status === 'IN_PROGRESS' ? 'bg-orange-50 text-orange-600 border-orange-100' : 'bg-teal-50 text-teal-600 border-teal-100') }}">
                                             {{ str_replace('_', ' ', $issue->status) }}
                                         </span>
                                     </div>
                                     <div class="text-xs text-gray-600 mb-2 line-clamp-2 leading-relaxed font-medium">{{ $issue->description }}</div>

                                     <div class="text-xs text-gray-600 mb-2 line-clamp-2 leading-relaxed font-medium">{{ $issue->description }}</div>

                                     <div class="flex items-center justify-between text-[10px] text-gray-400 font-bold uppercase tracking-wide border-t border-gray-50 pt-2">
                                         <div class="flex items-center gap-2">
                                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                             <span>{{ $issue->status === 'RESOLVED' ? 'Resolved ' : 'Updated ' }}{{ $issue->updated_at->diffForHumans() }}</span>
                                         </div>
                                         <div class="flex items-center gap-2">
                                             <div class="w-4 h-4 rounded-full bg-teal-100 flex items-center justify-center text-[8px] text-teal-700">
                                                 {{ substr($issue->reporter->name ?? 'S', 0, 1) }}
                                             </div>
                                             <span>{{ $issue->reporter->name ?? 'System' }}</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">No recent issues</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Overdue & Common Problems --}}
                <div class="space-y-6">
                    
                    {{-- Overdue Issues --}}
                    @if($overdueIssues->count() > 0)
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-xl border border-red-100">
                        <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-5 border-b border-red-200 rounded-t-2xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <h3 class="text-lg font-black text-white">Overdue Issues</h3>
                                            <p class="text-xs text-red-100 font-medium">Requires immediate attention</p>
                                        </div>
                                        <div x-data="{ isOpen: false }" class="relative">
                                            <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-red-200 hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                            <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-72 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-red-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                                <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-red-100 rotate-45"></div>
                                                <div class="relative">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                                        <div class="text-[10px] font-black text-red-600 uppercase tracking-widest">Maksud</div>
                                                    </div>
                                                    <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Daftar isu yang melewati batas waktu penyelesaian yang ditentukan.</div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                        <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                                    </div>
                                                    <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">cx_issues dengan created_at lebih dari 24 jam dan status 'OPEN' atau 'IN_PROGRESS'.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg text-xs font-black text-white animate-pulse">
                                    {{ $overdueIssues->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar">
                                 @foreach($overdueIssues as $issue)
                                     <div class="group p-4 bg-white rounded-2xl border border-red-100 hover:border-red-200 transition-all shadow-sm hover:shadow-md">
                                         <div class="flex items-start justify-between gap-3 mb-2">
                                             <div class="flex flex-col">
                                                 <span class="text-sm font-black text-gray-800 tracking-tight group-hover:text-red-600 transition-colors">{{ $issue->workOrder->spk_number ?? 'Unknown SPK' }}</span>
                                                 <span class="text-[10px] text-red-600 font-black uppercase tracking-widest mt-0.5 whitespace-nowrap">TERLAMBAT {{ number_format($issue->created_at->diffInDays(now()), 0) }} HARI</span>
                                             </div>
                                             <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center">
                                                 <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                             </div>
                                         </div>
                                         <div class="text-xs text-gray-500 mb-3 font-medium leading-relaxed">{{ $issue->category }}</div>

                                         <div class="text-xs text-gray-500 mb-3 font-medium leading-relaxed">{{ $issue->category }}</div>

                                         <div class="flex items-center justify-between border-t border-red-50 pt-3">
                                             <span class="text-[10px] font-black text-gray-400 tracking-tighter uppercase">{{ $issue->reported_by }}</span>
                                             <a href="{{ route('cx.index', ['search' => $issue->workOrder->spk_number]) }}" class="p-1 px-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all text-[10px] font-black uppercase tracking-widest shadow-sm">DETAILS</a>
                                         </div>
                                     </div>
                                 @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Common Problems --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div>
                                        <h3 class="text-lg font-black text-gray-800">Common Problems</h3>
                                        <p class="text-xs text-gray-500 font-medium">Top 5 categories</p>
                                    </div>
                                    <div x-data="{ isOpen: false }" class="relative">
                                        <button @click.stop="isOpen = !isOpen" @click.away="isOpen = false" class="text-gray-300 hover:text-orange-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <div x-show="isOpen" x-cloak x-transition class="absolute z-[100] w-72 max-w-none p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-orange-100 left-1/2 -translate-x-1/2 mt-2 whitespace-normal text-left">
                                            <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                            <div class="relative">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                                    <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                                                </div>
                                                <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Mengidentifikasi kategori masalah yang paling sering muncul untuk analisis akar masalah.</div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                                    <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                                </div>
                                                <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Agregasi kolom 'category' dari tabel cx_issues.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                 @foreach($commonProblems as $problem)
                                 <div class="group p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-orange-200 transition-all hover:bg-white hover:shadow-lg">
                                     <div class="flex justify-between items-end mb-3">
                                         <div class="flex flex-col">
                                             <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Masalah Dominan</span>
                                             <span class="text-sm font-black text-gray-800 group-hover:text-orange-600 transition-colors tracking-tight">{{ $problem->category }}</span>
                                         </div>
                                         <div class="flex flex-col items-end">
                                             <span class="text-lg font-black bg-gradient-to-br from-teal-600 to-orange-600 bg-clip-text text-transparent">{{ $problem->count }}</span>
                                             <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Kasus</span>
                                         </div>
                                     </div>
                                     <div class="w-full bg-gray-200/50 rounded-full h-2.5 overflow-hidden">
                                         <div class="h-full rounded-full bg-gradient-to-r from-teal-500 via-orange-400 to-orange-500 transition-all duration-1000 group-hover:shadow-[0_0_10px_rgba(249,115,22,0.3)]" 
                                              style="width: {{ min(($problem->count / ($commonProblems->max('count') ?: 1)) * 100, 100) }}%"></div>
                                     </div>
                                 </div>
                                 @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #14b8a6, #f97316);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #0d9488, #ea580c);
        }
    </style>

    {{-- Realtime Polling Script --}}
    <script>
        (function() {
            const POLL_INTERVAL = 30000;
            const API_URL = '{{ route("cx.dashboard.api-stats") }}' + '?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}';

            function updateCxDashboard() {
                fetch(API_URL, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    const updates = {
                        'cx-stat-total': data.total_issues,
                        'cx-stat-open': data.open_issues,
                        'cx-stat-inprogress': data.in_progress_issues,
                        'cx-stat-resolved': data.resolved_issues,
                        'cx-stat-cancelled': data.cancelled_issues,
                        'cx-stat-response': data.avg_response_time + 'h',
                        'cx-stat-resolution': data.resolution_rate + '%',
                    };
                    Object.entries(updates).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el && el.textContent != val) {
                            el.textContent = val;
                            el.classList.add('animate-pulse');
                            setTimeout(() => el.classList.remove('animate-pulse'), 1500);
                        }
                    });

                    // Update timestamp
                    const timeEl = document.querySelector('.cx-live-time');
                    if (timeEl) timeEl.textContent = 'Updated ' + data.timestamp;
                })
                .catch(err => console.warn('CX Dashboard poll error:', err));
            }

            setInterval(updateCxDashboard, POLL_INTERVAL);
        })();
    </script>

</x-app-layout>
