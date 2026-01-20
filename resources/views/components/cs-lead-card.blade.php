@props(['lead', 'isInvest' => false, 'isClosing' => false])

<div class="bg-white p-3 rounded-lg border shadow-sm hover:shadow-md transition-shadow relative group">
    {{-- Status Indicator Stripe --}}
    <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-lg 
        {{ $lead->status === 'NEW' ? 'bg-blue-500' : '' }}
        {{ $lead->status === 'KONSULTASI' ? 'bg-indigo-500' : '' }}
        {{ str_contains($lead->status, 'INVEST') ? 'bg-amber-500' : '' }}
        {{ $lead->status === 'CLOSING' ? 'bg-green-500' : '' }}
    "></div>

    <div class="pl-3">
        <div class="flex justify-between items-start mb-2">
            <h4 class="font-bold text-gray-800 text-sm truncate">{{ $lead->customer_phone }}</h4>
            <span class="text-[10px] text-gray-400">{{ $lead->created_at->format('d/m H:i') }}</span>
        </div>
        
        @if($lead->customer_name)
            <p class="text-xs text-indigo-600 font-semibold mb-1">{{ $lead->customer_name }}</p>
        @endif

        @if($lead->notes)
            <p class="text-xs text-gray-500 line-clamp-2 italic mb-2">"{{ $lead->notes }}"</p>
        @endif
        
        {{-- Invest Timer --}}
        @if($isInvest)
            <div class="mt-2 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded inline-block">
                ⚠️ Follow Up Needed
            </div>
        @endif

        {{-- Action Buttons (Visible on Hover?) - Let's make them always visible for clarity --}}
        <div class="mt-3 flex gap-2 justify-end border-t pt-2">
            
            @if($lead->status === 'NEW')
                <button onclick="updateLeadStatus({{ $lead->id }}, 'KONSULTASI')" class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-1 rounded border border-indigo-200 hover:bg-indigo-100 font-bold">
                    Mulai Konsul
                </button>
            @endif

            @if($lead->status === 'KONSULTASI' || str_contains($lead->status, 'INVEST'))
                <a href="{{ route('cs.leads.show', $lead->id) }}" class="text-[10px] bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200 hover:bg-green-100 font-bold flex items-center gap-1 decoration-0">
                    Deal / Buat SPK
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </a>
            @endif

            <form action="{{ route('cs.leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus Lead ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-[10px] bg-red-50 text-red-700 px-2 py-1 rounded border border-red-200 hover:bg-red-100 font-bold" title="Hapus Lead">
                    🗑️
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Simple inline script for status update (AJAX)
    // We can move this to the main dashboard script later
    function updateLeadStatus(id, status) {
        if(!confirm('Pindahkan status ke ' + status + '?')) return;

        fetch(`/cs/leads/${id}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload(); // Simple reload for now
            }
        });
    }

    function copyFormLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link Form berhasil disalin! Kirimkan ke customer.');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            // Fallback
            prompt("Salin link ini:", url);
        });
    }
</script>
