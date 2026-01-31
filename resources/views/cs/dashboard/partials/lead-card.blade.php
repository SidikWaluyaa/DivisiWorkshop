<div class="lead-card bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:border-[#22AF85] hover:shadow-md transition-all cursor-pointer group relative" 
     data-id="{{ $lead->id }}" 
     @click="goToDetail({{ $lead->id }})">
    {{-- Priority Indicator --}}
    @if($lead->priority === 'HOT')
        <div class="absolute -top-1 -right-1 w-3 h-3 bg-[#FFC232] rounded-full border-2 border-white animate-pulse shadow-sm"></div>
    @endif

    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full {{ $lead->priority === 'HOT' ? 'bg-[#FFC232]' : 'bg-[#22AF85]' }}"></span>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $lead->source }}</span>
        </div>
        <span class="text-[9px] font-bold text-gray-400">{{ $lead->created_at->diffForHumans(null, true) }}</span>
    </div>

    <div class="mb-3">
        <div class="font-black text-gray-900 text-sm leading-tight group-hover:text-[#22AF85] transition-colors truncate">{{ $lead->customer_name ?? 'Guest' }}</div>
        <div class="text-[10px] font-bold text-gray-500 mt-0.5">{{ $lead->customer_phone }}</div>
    </div>

    @if($lead->status === 'KONSULTASI')
        @php $latestQuotation = $lead->getLatestQuotation(); @endphp
        @if($latestQuotation)
            <div class="bg-gray-50 rounded-xl p-2 mb-3 border border-gray-100">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-[9px] font-black text-gray-400 uppercase">Quotation</span>
                    <span class="text-[9px] font-black {{ $latestQuotation->status === 'ACCEPTED' ? 'text-[#22AF85]' : 'text-[#FFC232]' }} uppercase">{{ $latestQuotation->status }}</span>
                </div>
                <div class="text-[11px] font-black text-gray-800">Rp {{ number_format($latestQuotation->total, 0, ',', '.') }}</div>
            </div>
        @endif
    @endif

    @if($lead->status === 'CLOSING' && $lead->spk)
        <div class="bg-gray-50 rounded-xl p-2 mb-3 border border-gray-100">
            <div class="flex justify-between items-center mb-1">
                <span class="text-[9px] font-black text-gray-400 uppercase">SPK</span>
                <span class="text-[9px] font-black {{ $lead->spk->dp_status === 'PAID' ? 'text-[#22AF85]' : 'text-orange-500' }} uppercase">{{ $lead->spk->label }}</span>
            </div>
            <div class="text-[11px] font-black text-gray-800">{{ $lead->spk->spk_number }}</div>
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex -space-x-2">
            @if($lead->next_follow_up_at)
                <div class="w-6 h-6 rounded-full bg-orange-50 border-2 border-white flex items-center justify-center text-orange-500 shadow-sm" title="Follow Up: {{ $lead->next_follow_up_at->format('d M H:i') }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            @endif
            @if($lead->response_time_minutes && $lead->response_time_minutes <= 15)
                <div class="w-6 h-6 rounded-full bg-green-50 border-2 border-white flex items-center justify-center text-green-500 shadow-sm" title="Fast Response: {{ $lead->response_time_formatted }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
            @endif
        </div>
        
        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ $lead->wa_greeting_link }}" target="_blank" @click.stop class="w-7 h-7 rounded-lg bg-green-500 text-white flex items-center justify-center shadow-sm hover:bg-green-600">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.29-4.143c1.589.943 3.133 1.415 4.742 1.416 5.42.003 9.83-4.412 9.832-9.832 0-2.628-1.023-5.097-2.88-6.956-1.856-1.859-4.325-2.88-6.952-2.882-5.43 0-9.84 4.412-9.842 9.832-.001 1.736.469 3.426 1.36 4.918l-1.003 3.663 3.743-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            </a>
        </div>
    </div>
</div>
