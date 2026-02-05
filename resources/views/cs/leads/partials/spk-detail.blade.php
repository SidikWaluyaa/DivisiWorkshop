{{-- SPK Section --}}
@if($lead->spk)
    <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100 mb-6">
        <div class="p-6 pb-0">
            <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Service Production Key (SPK)</h3>
            <div class="w-12 h-1.5 bg-[#22AF85] rounded-full mt-2"></div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-xs text-gray-500 font-semibold">SPK Number</label>
                    <p class="text-lg font-bold text-gray-900">{{ $lead->spk->spk_number }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-semibold">Status</label>
                    <p><span class="px-2 py-1 rounded text-xs font-semibold {{ $lead->spk->status_badge_class }}">{{ $lead->spk->label }}</span></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-[1.5rem] p-5 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Total Price:</span>
                    <span class="font-bold">Rp {{ number_format($lead->spk->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">DP Amount:</span>
                    <span class="font-semibold text-yellow-600">Rp {{ number_format($lead->spk->dp_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Remaining:</span>
                    <span class="font-semibold text-red-600">Rp {{ number_format($lead->spk->remaining_payment, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-xs text-gray-500 font-semibold">DP Status</label>
                <div class="mt-1">
                    <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest {{ $lead->spk->dp_status_badge_class }}">
                        {{ $lead->spk->dp_status }}
                    </span>
                </div>
            </div>

            @if($lead->spk->status === 'WAITING_DP')
                <button onclick="openDpModal()" class="w-full bg-[#22AF85] hover:bg-[#22AF85]/90 text-white py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-100 transition transform hover:-translate-y-1 mb-2">
                    💰 Konfirmasi DP Dibayar
                </button>
            @elseif($lead->spk->status === 'WAITING_VERIFICATION')
                <div class="w-full bg-[#FFC232]/10 text-gray-900 py-3.5 px-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-center mb-2 border-2 border-[#FFC232]/20">
                    ⏳ Menunggu Verifikasi Finance
                </div>
            @elseif($lead->spk->status === 'HANDED_TO_WORKSHOP')
                <div class="w-full bg-[#22AF85]/10 text-[#22AF85] py-3.5 px-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-center mb-2 border-2 border-[#22AF85]/20">
                    ✅ Sudah Diserahkan ke Workshop
                </div>
            @elseif($lead->spk->canBeHandedToWorkshop())
                <button onclick="openHandoverModal()" class="w-full bg-[#22AF85] hover:bg-[#22AF85]/90 text-white py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-100 transition transform hover:-translate-y-1 mb-2">
                    🚚 Serahkan ke Workshop
                </button>
            @endif

            {{-- PDF Download --}}
            <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                <a href="{{ route('cs.spk.export-pdf', $lead->spk->id) }}" target="_blank" class="flex items-center justify-center gap-3 bg-gray-50 hover:bg-white border-2 border-gray-100 hover:border-[#22AF85]/20 text-gray-900 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition shadow-sm hover:shadow-xl hover:-translate-y-1">
                    <svg class="w-5 h-5 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Download PDF SPK
                </a>
                <a href="{{ route('cs.spk.shipping-label', $lead->spk->id) }}" target="_blank" class="flex items-center justify-center gap-3 bg-[#f0fdf4] hover:bg-white border-2 border-[#22AF85]/10 hover:border-[#22AF85]/30 text-gray-900 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition shadow-sm hover:shadow-xl hover:-translate-y-1">
                    <svg class="w-5 h-5 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Resi Pengiriman (WA)</span>
                </a>
            </div>
        </div>
    </div>
@endif
