{{-- Quotations Section --}}
@if(in_array($lead->status, ['KONSULTASI', 'CLOSING', 'CONVERTED']))
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
        <div class="p-8 pb-4 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-2xl">Quotations History</h3>
                <div class="w-16 h-2 bg-[#FFC232] rounded-full mt-2.5"></div>
            </div>
        </div>
        <div class="p-8">
            @forelse($lead->quotations as $quotation)
                <div class="border-2 rounded-[2rem] p-6 mb-6 {{ $quotation->status === 'ACCEPTED' ? 'border-[#22AF85]/20 bg-[#22AF85]/5' : 'border-gray-50 bg-white shadow-sm' }}">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg uppercase tracking-widest">{{ $quotation->quotation_number }}</h4>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Versi {{ $quotation->version }} • {{ $quotation->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest leading-none {{ $quotation->status === 'ACCEPTED' ? 'bg-[#22AF85] text-white' : 'bg-gray-100 text-gray-500' }}">
                            {{ $quotation->status }}
                        </span>
                    </div>
                    
                    {{-- Items --}}
                    <div class="bg-white/50 border border-gray-100/50 rounded-2xl p-4 mb-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                            Data Barang ({{ count($quotation->quotationItems ?? []) }} unit)
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($quotation->quotationItems as $item)
                                <div class="bg-white rounded-2xl p-4 border border-gray-50 shadow-sm hover:shadow-md transition group">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-xl group-hover:bg-[#22AF85]/10 transition duration-300">
                                            {{ $item->category_icon ?? '📦' }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between">
                                                <p class="font-black text-xs text-gray-900 uppercase tracking-tight">{{ $item->label }}</p>
                                                <button data-item="{{ json_encode($item) }}" onclick="openEditItemModal({{ $item->id }}, this)" class="text-gray-300 hover:text-[#22AF85] transition" title="Edit Item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </button>
                                            </div>
                                            <div class="mt-2 space-y-1">
                                                <p class="text-[10px] font-bold text-gray-500"><span class="text-gray-400">ID:</span> #{{ $item->item_number }}</p>
                                                @if($item->shoe_color)
                                                    <p class="text-[10px] font-bold text-gray-500"><span class="text-gray-400">Color:</span> {{ $item->shoe_color }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if($quotation->status === 'ACCEPTED')
                            <button onclick="rejectQuotation({{ $quotation->id }})" class="bg-white hover:bg-red-50 text-red-500 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition border-2 border-red-50">
                                ❌ Batalkan
                            </button>
                        @endif

                        <a href="{{ route('cs.quotations.export-pdf', $quotation->id) }}" target="_blank" class="bg-[#22AF85] hover:bg-[#22AF85]/90 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition shadow-lg shadow-green-100 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Download PDF
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-gray-100 text-gray-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Belum ada quotation</p>
                </div>
            @endforelse
        </div>
    </div>
@endif
