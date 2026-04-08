<div>
    {{-- Header stats could go here if needed to be reactive --}}
    
    {{-- Premium Filters --}}
    <div class="relative bg-white backdrop-blur-2xl rounded-[2rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 mb-8 group overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#22AF85]/5 to-[#22AF85]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        <div class="relative grid grid-cols-1 md:grid-cols-4 gap-5">
            {{-- Search Bar --}}
            <div class="relative">
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Pencarian</label>
                <div class="relative flex items-center">
                    <div class="absolute left-4 z-10 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        class="w-full bg-gray-50/50 backdrop-blur-sm border border-gray-100 placeholder:text-gray-400 rounded-2xl pl-11 pr-10 py-3.5 text-sm focus:bg-white focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85]/40 transition-all font-bold text-gray-800"
                        placeholder="SPK / Nama / No. Telp...">
                    <div wire:loading wire:target="search" class="absolute right-4">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#22AF85] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-[#22AF85]"></span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Response Dropdown --}}
            <div>
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Status Respon</label>
                <div class="relative">
                    <select wire:model.live="response" class="w-full appearance-none bg-gray-50/50 backdrop-blur-sm border border-gray-100 rounded-2xl px-4 py-3.5 text-sm focus:bg-white focus:ring-4 focus:ring-[#22AF85]/10 focus:border-[#22AF85]/40 transition-all font-bold text-gray-800 cursor-pointer">
                        <option value="">Semua Respon</option>
                        @foreach(['Puas', 'Komplain', 'Kurang Puas', 'No Respon 1x24 Jam', 'Hold'] as $res)
                            <option value="{{ $res }}">{{ $res }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Date Range --}}
            <div class="md:col-span-1">
                <label class="text-[9px] font-black text-gray-400/80 uppercase tracking-[0.2em] ml-2 mb-2 block transition-colors group-hover:text-[#22AF85]">Rentang Waktu</label>
                <div class="flex items-center gap-2 bg-gray-50/50 backdrop-blur-sm border border-gray-100 rounded-2xl p-1 focus-within:bg-white focus-within:ring-4 focus-within:ring-[#22AF85]/10 focus-within:border-[#22AF85]/40 transition-all">
                    <input type="date" wire:model.live="startDate" class="flex-1 bg-transparent border-none px-2 py-2.5 text-xs font-bold text-gray-700 outline-none focus:ring-0">
                    <span class="text-gray-300 font-black">—</span>
                    <input type="date" wire:model.live="endDate" class="flex-1 bg-transparent border-none px-2 py-2.5 text-xs font-bold text-gray-700 outline-none focus:ring-0">
                </div>
            </div>

            {{-- Reset Button --}}
            <div class="flex items-end text-right h-full">
                <button wire:click="resetFilters" class="w-full h-[52px] bg-white border border-gray-100 hover:border-[#FFC232]/50 hover:bg-[#FFC232]/5 hover:text-[#FFC232] text-gray-500 font-black rounded-2xl transition-all duration-300 flex items-center justify-center gap-2 text-[10px] uppercase tracking-[0.2em] shadow-sm hover:shadow active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    RESET FILTER
                </button>
            </div>
        </div>
    </div>

    {{-- Premium Data Table --}}
    <div class="bg-white backdrop-blur-2xl rounded-[2.5rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative transition-all duration-300">
        {{-- Custom Loading Overlay --}}
        <div wire:loading.delay class="absolute inset-0 bg-white/60 backdrop-blur-md z-20 flex items-center justify-center rounded-[2.5rem]">
             <div class="flex flex-col items-center gap-4 bg-white px-8 py-6 rounded-3xl shadow-xl border border-gray-100">
                <svg class="animate-spin h-10 w-10 text-[#22AF85]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-100" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-black text-gray-800 uppercase tracking-[0.2em]">Memuat Data...</span>
             </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100/80">
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] w-40">Waktu Masuk</th>
                        <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Detail SPK & Customer</th>
                        <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">PIC & Waktu Kontak</th>
                        <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Respon Kepuasan</th>
                        <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50/80 active:bg-gray-100 transition-colors duration-200 group relative">
                        {{-- Data: Date with Hover indicator rail --}}
                        <td class="px-8 py-5 align-top relative">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#22AF85] opacity-0 group-hover:opacity-100 transition-opacity rounded-r-full"></div>
                            
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-black text-gray-800">{{ $item->entered_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">{{ $item->entered_at->format('H:i') }} WIB</span>
                            </div>
                        </td>
                        
                        {{-- Data: Info --}}
                        <td class="px-6 py-5 align-top">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black text-[#22AF85] bg-[#22AF85]/10 px-2 py-0.5 rounded-lg border border-[#22AF85]/20 uppercase tracking-tight">{{ $item->workOrder->spk_number }}</span>
                                </div>
                                <span class="text-sm font-black text-gray-900">{{ $item->workOrder->customer_name }}</span>
                                <div class="text-xs font-bold text-gray-500 flex items-center gap-1.5 select-all">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $item->workOrder->customer_phone }}
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ $item->workOrder->shoe_brand }} - {{ $item->workOrder->shoe_color }}</span>
                            </div>
                        </td>
                        
                        {{-- Data: PIC --}}
                        <td class="px-6 py-5 align-top">
                            @if($item->pic)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#22AF85] flex items-center justify-center shadow-lg shadow-[#22AF85]/20 text-white font-black text-xs">
                                    {{ substr($item->pic->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs font-black text-gray-800 leading-tight uppercase">{{ $item->pic->name }}</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{ $item->contacted_at?->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            @else
                            <div class="inline-flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 border border-gray-100 rounded-xl relative group cursor-help text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Belum Dikontak
                            </div>
                            @endif
                        </td>
                        
                        {{-- Data: Response Status --}}
                        <td class="px-6 py-5 align-top">
                            @php
                                $colorMap = [
                                    'Puas' => ['bg' => 'bg-[#22AF85]/10', 'text' => 'text-[#22AF85]', 'border' => 'border-[#22AF85]/20', 'dot' => 'bg-[#22AF85]'],
                                    'Komplain' => ['bg' => 'bg-[#FFC232]', 'text' => 'text-gray-900', 'border' => 'border-[#FFC232]', 'dot' => 'bg-white'],
                                    'Kurang Puas' => ['bg' => 'bg-[#FFC232]', 'text' => 'text-gray-900', 'border' => 'border-[#FFC232]', 'dot' => 'bg-white'],
                                    'No Respon 1x24 Jam' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                                    'Hold' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                                ];
                                $style = $colorMap[$item->response] ?? null;
                            @endphp
                            
                            @if($style)
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl {{ $style['bg'] }} {{ $style['border'] }} border">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $style['text'] }}">
                                        {{ $item->response }}
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
                                    <span class="flex h-1.5 w-1.5 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC232] opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#FFC232]"></span>
                                    </span>
                                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">
                                        Menunggu Respon
                                    </span>
                                </div>
                            @endif
                        </td>
                        
                        {{-- Data: Action --}}
                        <td class="px-8 py-5 align-top text-right">
                            <button wire:click="edit({{ $item->id }})" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white text-[#22AF85] rounded-xl hover:bg-[#22AF85]/5 hover:border-[#22AF85]/30 transition-all shadow-sm shadow-black/5 border border-gray-100 active:scale-95 group/btn">
                                <span class="text-[10px] font-black uppercase tracking-widest opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all hidden sm:block">Perbarui</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center border-none">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-24 h-24 mb-6 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 shadow-inner">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.675.337a6 6 0 01-3.86.517l-2.388-.477a2 2 0 00-1.023.547l-1.318 1.319a2 2 0 00-.566 1.104l-1.49 10.161a2 2 0 00.358 1.581l4.411 5.415a2 2 0 003.088 0l4.411-5.415a2 2 0 00.358-1.581l-1.49-10.161a2 2 0 00-.566-1.104l-1.317-1.319z"></path></svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight mb-2">Tidak Ada Data Konfirmasi</h3>
                                <p class="text-xs font-medium text-gray-500 leading-relaxed">Belum ada pelanggan yang siap dikonfirmasi dalam rentang waktu atau filter ini. Coba sesuaikan filter pencarian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($items->hasPages())
        <div class="p-6 border-t border-gray-100/50 bg-gray-50/30">
            {{ $items->links() }}
        </div>
        @endif
    </div>

    {{-- Premium Edit Modal --}}
    <div x-data="{ open: false }" 
         x-on:open-edit-modal.window="open = true" 
         x-on:close-edit-modal.window="open = false"
         x-cloak>
        <x-modal-custom x-show="open" @close="open = false" title="📝 Konfirmasi Status">
            <form wire:submit.prevent="save" class="space-y-6">
                {{-- Detail Pelanggan Info Box --}}
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-[1.5rem] p-5 border border-gray-200/60 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#FFC232]/10 rounded-full blur-xl"></div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-1">Pelanggan</label>
                    <div class="text-lg font-black text-gray-800 tracking-tight">
                        {{ $editingId ? App\Models\CxAfterConfirmation::find($editingId)?->workOrder->customer_name : '' }}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5">
                    {{-- Select Respon --}}
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2 px-1">Opsi Respon</label>
                        <div class="relative">
                            <select wire:model="editingResponse" class="w-full appearance-none bg-white border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-black text-gray-700 hover:border-[#22AF85]/40 focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 transition-all cursor-pointer shadow-sm">
                                <option value="">— Pilih Respon Customer —</option>
                                @foreach(['Puas', 'Komplain', 'Kurang Puas', 'No Respon 1x24 Jam', 'Hold'] as $res)
                                    <option value="{{ $res }}">{{ $res }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Textarea Note --}}
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2 px-1">Catatan Tambahan (Opsional)</label>
                        <textarea wire:model="editingNotes" rows="3" class="w-full bg-white border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-medium text-gray-700 placeholder:text-gray-300 hover:border-[#22AF85]/40 focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 transition-all shadow-sm" placeholder="Tulis catatan historis panggilan disini..."></textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 flex gap-3">
                    <button type="button" @click="open = false" class="px-8 py-4 bg-white border-2 border-gray-100 hover:bg-gray-50 hover:border-gray-200 rounded-[1.5rem] text-xs font-black text-gray-500 transition-all uppercase tracking-widest active:scale-95 shadow-sm">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-4 bg-[#FFC232] hover:bg-[#F2B01E] rounded-[1.5rem] text-xs font-black text-gray-900 shadow-[0_8px_20px_-6px_rgba(255,194,50,0.5)] transition-all uppercase tracking-[0.2em] active:scale-95 border border-[#E5A71B] relative overflow-hidden group">
                        <div class="absolute inset-0 bg-white/30 group-hover:translate-x-full transition-transform duration-500 -skew-x-12 -ml-8 w-20"></div>
                        <span wire:loading.remove wire:target="save" class="flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Data
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center gap-3">
                             <svg class="animate-spin h-4 w-4 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-100" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </x-modal-custom>
    </div>

    @script
    <script>
        $wire.on('notify', (event) => {
            alert(event[0].message);
        });
    </script>
    @endscript
</div>
