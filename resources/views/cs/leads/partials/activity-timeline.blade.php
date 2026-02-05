{{-- Activity Timeline --}}
<div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
    <div class="p-8 pb-4 bg-gray-50/50">
        <h3 class="font-black text-gray-900 uppercase tracking-tighter text-2xl">Customer Journey</h3>
        <div class="w-16 h-2 bg-gray-200 rounded-full mt-2.5"></div>
    </div>
    <div class="p-8 max-h-[600px] overflow-y-auto custom-scrollbar">
        <div class="relative">
            {{-- Vertical Line --}}
            <div class="absolute left-[23px] top-0 bottom-0 w-1 bg-gray-100 rounded-full"></div>

            @forelse($lead->activities as $activity)
                <div class="relative pl-16 mb-10 last:mb-0 group/item">
                    {{-- Dot / Icon --}}
                    <div class="absolute left-0 top-0 w-12 h-12 rounded-2xl bg-white border-2 border-gray-100 shadow-sm flex items-center justify-center text-xl z-10 transition-all duration-300 group-hover/item:scale-110 group-hover/item:border-[#22AF85]/30 group-hover/item:shadow-lg group-hover/item:shadow-[#22AF85]/10">
                        {{ $activity->type_icon }}
                    </div>

                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                            <span class="font-black text-xs text-gray-900 uppercase tracking-widest">{{ $activity->user->name ?? 'System' }}</span>
                            <div class="hidden sm:block w-1 h-1 rounded-full bg-gray-200"></div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.1em]">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="p-5 bg-gray-50 rounded-2xl border-2 border-transparent transition-all duration-300 group-hover/item:bg-white group-hover/item:border-gray-100 group-hover/item:shadow-sm">
                            <p class="text-sm text-gray-700 font-medium leading-relaxed">{!! $activity->formatted_content !!}</p>
                            @if($activity->channel)
                                <div class="mt-3 pt-3 border-t border-gray-100/50 flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-gray-100 rounded text-[9px] font-black text-gray-400 uppercase tracking-widest">Via {{ $activity->channel }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 border-2 border-dashed border-gray-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum ada jejak aktivitas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #e5e5e5;
}
</style>
