{{-- Modal: Handover to Workshop --}}
<div id="handoverModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
        {{-- Modal Header --}}
        <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Gudang Handover</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Warehouse Entry Control</p>
                </div>
            </div>
            <button onclick="closeHandoverModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 transition-all duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-10">
            @if($lead->spk && count($lead->spk->items ?? []) > 0)
                <form action="{{ route('cs.spk.hand-to-workshop', $lead->spk->id) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf
                    <div class="p-6 bg-[#22AF85]/5 border-2 border-dashed border-[#22AF85]/20 rounded-3xl">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-[#22AF85] rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg shadow-green-100">
                                📦
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-900 uppercase tracking-tighter">Logistics Confirmation</p>
                                <p class="text-[10px] text-[#22AF85] font-black uppercase tracking-widest">Converting {{ count($lead->spk->items ?? []) }} SPK Items into Work Orders</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach($lead->spk->items as $spkItem)
                            <div class="bg-gray-50/50 rounded-[2.5rem] p-8 border-2 border-gray-100 group">
                                <div class="flex justify-between items-start mb-8 pb-6 border-b border-gray-100">
                                    <div class="flex items-center gap-5">
                                        <div class="text-4xl bg-white w-16 h-16 rounded-2xl flex items-center justify-center shadow-sm border border-gray-100">{{ $spkItem->category_icon }}</div>
                                        <div>
                                            <h5 class="text-xl font-black text-gray-900">Item #{{ $spkItem->item_number }}</h5>
                                            <p class="text-[10px] text-[#22AF85] font-black uppercase tracking-widest mt-1">{{ $spkItem->category }} | {{ $spkItem->shoe_brand ?: 'Generic' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Item Value</p>
                                        <p class="text-lg font-black text-gray-900">Rp {{ number_format($spkItem->item_total_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {{-- Physical Details --}}
                                    <div class="space-y-4">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Physical Specs Verification</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <input type="text" name="items[{{ $spkItem->id }}][shoe_brand]" value="{{ $spkItem->shoe_brand }}" placeholder="Merk"
                                                   class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                            <input type="text" name="items[{{ $spkItem->id }}][shoe_type]" value="{{ $spkItem->shoe_type }}" placeholder="Tipe"
                                                   class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                            <input type="text" name="items[{{ $spkItem->id }}][shoe_color]" value="{{ $spkItem->shoe_color }}" placeholder="Warna"
                                                   class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                            <input type="text" name="items[{{ $spkItem->id }}][shoe_size]" value="{{ $spkItem->shoe_size }}" placeholder="Ukuran"
                                                   class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                        </div>
                                        <div class="pt-4">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Reference Photo Attachment</label>
                                            <input type="file" name="items[{{ $spkItem->id }}][ref_photo]" accept="image/*" 
                                                   class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#22AF85]/10 file:text-[#22AF85] hover:file:bg-[#22AF85]/20 cursor-pointer">
                                        </div>
                                    </div>

                                    {{-- Service Pipeline --}}
                                    <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm space-y-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Validated Service Pipeline</label>
                                            <span class="text-[9px] font-black text-[#22AF85] uppercase tracking-widest">{{ count($spkItem->services ?? []) }} Services</span>
                                        </div>
                                        <div class="space-y-3 max-h-[200px] overflow-y-auto custom-scrollbar">
                                            @forelse(($spkItem->services ?? []) as $service)
                                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-[#22AF85]/30 transition-all group">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-[#22AF85]"></div>
                                                                <span class="text-[11px] font-black text-gray-900 uppercase tracking-wide">{{ $service['name'] }}</span>
                                                                @if(!empty($service['is_custom']))
                                                                    <span class="px-2 py-0.5 bg-[#FFC232] text-gray-900 rounded text-[8px] font-black uppercase tracking-widest">Custom</span>
                                                                @endif
                                                            </div>
                                                            @if(!empty($service['manual_detail']) || !empty($service['description']))
                                                                <p class="text-[10px] text-gray-500 font-medium pl-3.5 italic leading-relaxed">
                                                                    "{{ $service['manual_detail'] ?? $service['description'] ?? '' }}"
                                                                </p>
                                                            @endif
                                                        </div>
                                                        <div class="text-right flex-shrink-0">
                                                            <span class="text-xs font-black text-[#22AF85]">Rp {{ number_format($service['price'] ?? 0, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-6 text-gray-400">
                                                    <p class="text-[10px] font-bold uppercase tracking-widest">Tidak ada layanan terpilih</p>
                                                </div>
                                            @endforelse
                                        </div>
                                        {{-- Service Subtotal --}}
                                        @php
                                            $serviceSubtotal = collect($spkItem->services ?? [])->sum('price');
                                        @endphp
                                        <div class="pt-4 border-t border-dashed border-gray-200 flex justify-between items-center">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal Jasa</span>
                                            <span class="text-sm font-black text-gray-900">Rp {{ number_format($serviceSubtotal, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="items[{{ $spkItem->id }}][item_type]" value="{{ $spkItem->category_prefix }}">
                                <input type="hidden" name="items[{{ $spkItem->id }}][spk_item_id]" value="{{ $spkItem->id }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- Warehouse Finalization --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-10 border-t border-gray-100 items-center">
                        <div class="p-8 bg-gray-900 rounded-[2.5rem] shadow-2xl text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Total Handover Value</p>
                                    <h4 class="text-3xl font-black tracking-tighter">Rp {{ number_format($lead->spk->total_price, 0, ',', '.') }}</h4>
                                </div>
                                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-3xl">📥</div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="button" onclick="closeHandoverModal()" 
                                    class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all transform hover:-translate-y-1">
                                Relay to Warehouse
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="p-10 text-center">
                    <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">⚠️</div>
                    <h4 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Ready SPK Required</h4>
                    <p class="text-sm text-gray-500 font-bold mt-2">Generate and accept an SPK first before physical handover to warehouse.</p>
                    <button onclick="closeHandoverModal()" class="mt-8 px-10 py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest">Understood</button>
                </div>
            @endif
        </div>
    </div>
</div>
