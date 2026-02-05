{{-- Premium Header --}}
<div class="mb-8">
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden relative group transition-all duration-500 hover:shadow-gray-300/50">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#22AF85]/5 to-transparent rounded-full -mr-20 -mt-20 blur-3xl opacity-50 transition-opacity group-hover:opacity-100"></div>
        
        <div class="p-8 md:p-10 relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
            <div class="flex items-center gap-8">
                {{-- Avatar / Icon --}}
                <div class="relative flex-shrink-0">
                    <div class="absolute -inset-1 bg-gradient-to-r from-[#22AF85] to-[#FFC232] rounded-3xl blur opacity-25 group-hover:opacity-60 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-[#22AF85] shadow-xl border border-gray-100 overflow-hidden transform transition-all duration-500 group-hover:scale-105 group-hover:rotate-3">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                </div>

                {{-- Lead Info --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-4 flex-wrap">
                        <h2 class="font-black text-4xl md:text-5xl text-gray-900 leading-tight tracking-tighter uppercase mb-0">
                            {{ $lead->customer_name ?? 'Guest' }}
                        </h2>
                        <div class="px-4 py-1.5 bg-gray-50 rounded-xl border-2 border-gray-100 text-[11px] font-black text-gray-400 tracking-widest uppercase shadow-sm">
                            ID: #{{ str_pad($lead->id, 5, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 flex-wrap">
                        {{-- Status Badge --}}
                        <div class="relative group/badge">
                            <div class="absolute -inset-0.5 bg-[#22AF85] rounded-xl blur opacity-20 group-hover/badge:opacity-40 transition"></div>
                            <span class="relative px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest bg-[#22AF85] text-white flex items-center gap-2 shadow-lg shadow-green-100 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                {{ $lead->status }}
                            </span>
                        </div>

                        {{-- Priority Badge --}}
                        <div class="relative group/badge">
                            <div class="absolute -inset-0.5 bg-[#FFC232] rounded-xl blur opacity-20 group-hover/badge:opacity-40 transition"></div>
                            <span class="relative px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest border-2 border-[#FFC232]/30 text-gray-900 bg-[#FFC232]/10 flex items-center gap-2 transition-all group-hover/badge:bg-[#FFC232]/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                                {{ $lead->priority }}
                            </span>
                        </div>

                        <div class="hidden md:block w-1.5 h-1.5 rounded-full bg-gray-200"></div>
                        
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Inscribed: {{ $lead->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Action Area --}}
            <div class="flex items-center gap-4 w-full md:w-auto mt-4 md:mt-0 pt-6 md:pt-0 border-t md:border-0 border-gray-100">
                <a href="{{ route('cs.dashboard') }}" 
                   class="flex-1 md:flex-initial group relative flex items-center justify-center gap-3 px-10 py-4 bg-gray-50 hover:bg-white text-gray-900 border-2 border-gray-100 hover:border-[#22AF85]/20 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-sm hover:shadow-2xl hover:shadow-[#22AF85]/10 hover:-translate-y-1 overflow-hidden">
                    <svg class="w-5 h-5 text-[#22AF85] group-hover:-translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Back to Base</span>
                </a>
            </div>
        </div>
    </div>
</div>
