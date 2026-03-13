<div>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-gray-800 tracking-tight">Aktivitas Terbaru</h3>
                            {{-- Info Tooltip --}}
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @mouseenter="open = true" @mouseleave="open = false" class="text-gray-300 hover:text-orange-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition class="absolute z-50 w-80 max-w-none p-5 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-orange-100 left-0 mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Log aktivitas real-time yang mencatat setiap perubahan status atau pembaruan penting yang dilakukan pada SPK.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Catatan Riwayat Sistem (Audit Log) setiap Surat Perintah Kerja yang diperbarui secara sistematis.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Latest workshop updates</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-bold animate-pulse">● Live</span>
            </div>
        </div>
        <div class="p-5">
            @if($logs && $logs->count() > 0)
            <div class="space-y-2 max-h-[350px] overflow-y-auto custom-scrollbar">
                @foreach($logs as $log)
                <div class="flex gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all border border-gray-100 hover:border-gray-200">
                    <div class="flex-shrink-0 mt-1.5">
                        <div class="w-2 h-2 rounded-full bg-teal-500 ring-4 ring-teal-100"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800 mb-0.5">
                            {{ $log->user->name ?? 'System' }}
                            <span class="font-normal text-gray-500">→</span>
                            <span class="text-teal-600">{{ $log->workOrder?->spk_number ?? 'Unknown' }}</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-1 line-clamp-1">{{ $log->description }}</div>
                        <div class="text-[10px] text-gray-400 font-medium">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">Belum ada aktivitas</p>
            </div>
            @endif
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #14b8a6, #f97316); border-radius: 10px; }
    </style>
</div>
