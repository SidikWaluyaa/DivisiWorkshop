<div>
    {{-- Loading Skeleton --}}
    <div wire:loading wire:target="loadData" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @for($i = 0; $i < 6; $i++)
        <div class="bg-white rounded-2xl p-5 shadow-lg animate-pulse">
            <div class="w-12 h-12 bg-gray-200 rounded-xl mb-3"></div>
            <div class="h-8 bg-gray-200 rounded w-16 mb-2"></div>
            <div class="h-3 bg-gray-100 rounded w-20"></div>
        </div>
        @endfor
    </div>

    {{-- Actual Content --}}
    <div wire:loading.remove wire:target="loadData" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- In Progress --}}
        <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-teal-100 hover:border-teal-200 cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                {{-- Tooltip --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="text-teal-300 hover:text-teal-600 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 mt-3 whitespace-normal">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
                                <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Maksud</div>
                            </div>
                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Jumlah SPK yang saat ini sedang aktif dikerjakan di workshop (Status Prep hingga QC).</div>
                            
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                            </div>
                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Sistem Antrean Antrian Workshop (Real-time).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-teal-600 mb-1">{{ $inProgress }}</div>
            <div class="text-xs font-bold text-teal-500 uppercase tracking-wider">Diproses</div>
        </div>

        {{-- Completed --}}
        <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-emerald-100 hover:border-emerald-200 cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                {{-- Tooltip --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="text-emerald-300 hover:text-emerald-600 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 mt-3 whitespace-normal">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                                <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Maksud</div>
                            </div>
                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Total SPK yang telah selesai dikerjakan dalam rentang waktu yang dipilih.</div>
                            
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                            </div>
                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Data Historis SPK (Status SELESAI).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-600 mb-1">{{ $throughput }}</div>
            <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Selesai</div>
        </div>

        {{-- Urgent --}}
        <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-amber-100 hover:border-amber-200 cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                {{-- Tooltip --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="text-amber-300 hover:text-amber-600 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-amber-500 rounded-full"></div>
                                <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Maksud</div>
                            </div>
                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Order yang sisa waktu pengerjaannya kurang dari 3 hari.</div>
                            
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                            </div>
                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Selisih hari ini dengan Estimasi Selesai (TAT).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-amber-600 mb-1">{{ $urgentCount }}</div>
            <div class="text-xs font-bold text-amber-500 uppercase tracking-wider">Mendesak</div>
        </div>

        {{-- Overdue --}}
        <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100 hover:border-red-200 cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                {{-- Tooltip --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="text-red-300 hover:text-red-600 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                <div class="text-[10px] font-black text-red-600 uppercase tracking-widest">Maksud</div>
                            </div>
                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Order yang melewati tanggal estimasi namun belum selesai.</div>
                            
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                            </div>
                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Audit status pengerjaan vs Target Deadline (SLA).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-red-600 mb-1">{{ $overdueCount }}</div>
            <div class="text-xs font-bold text-red-500 uppercase tracking-wider">Terlambat</div>
        </div>

        {{-- QC Pass Rate --}}
        <div class="group bg-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 border border-orange-100 hover:border-orange-200 cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                {{-- Tooltip --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="text-orange-300 hover:text-orange-600 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 right-0 mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 right-1 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                            </div>
                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Persentase order yang selesai tanpa melalui proses revisi QC.</div>
                            
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                            </div>
                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Log Quality Control (First Time Right Rate).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-orange-600 mb-1">{{ $qcPassRate }}%</div>
            <div class="text-xs font-bold text-orange-500 uppercase tracking-wider">Lolos QC</div>
        </div>

        {{-- Revenue --}}
        <div class="group bg-gradient-to-br from-teal-500 to-orange-500 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                {{-- Tooltip --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click.stop="open = !open" class="text-white/40 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 right-0 mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 right-1 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
                                <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Maksud</div>
                            </div>
                            <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Estimasi pendapatan jasa dari seluruh order yang telah selesai.</div>
                            
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                            </div>
                            <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Nilai total servis (Service Price) pada SPK status SELESAI.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-2xl font-black text-white mb-1 whitespace-nowrap overflow-hidden text-ellipsis">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
            <div class="text-xs font-bold text-white/90 uppercase tracking-wider">Pendapatan</div>
        </div>
    </div>
</div>
