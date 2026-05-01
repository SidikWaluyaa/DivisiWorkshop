<div class="p-6 space-y-4 bg-[#F8F9FA] min-h-screen relative font-sans">
    <!-- Header: Ultra Slim -->
    <div class="flex items-center justify-between max-w-[1600px] mx-auto">
        <div class="flex items-center space-x-3">
            <a href="{{ route('storage.disbursement.index') }}" class="p-1.5 bg-white rounded-lg shadow-sm border border-gray-100 text-gray-400 hover:text-rose-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 leading-none tracking-tight">{{ $disbursementId ? 'EDIT' : 'BARU' }} <span class="text-rose-500">BARANG KELUAR</span></h1>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">DISTRIBUSI MATERIAL WORKSHOP</p>
            </div>
        </div>
        
        <div class="bg-white px-4 py-1.5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="text-right">
                <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest block leading-none">DOKUMEN ID</span>
                <p class="text-[11px] font-black text-rose-500 leading-none mt-1">{{ $disbursement_number }}</p>
            </div>
            <div class="w-px h-6 bg-gray-100"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full {{ $status === 'COMPLETED' ? 'bg-green-500' : 'bg-rose-500' }}"></div>
                <span class="text-[9px] font-black text-gray-900 uppercase tracking-widest">{{ $status }}</span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-4 max-w-[1600px] mx-auto pb-24">
        <!-- Main Form Data -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">No. Referensi (Opsional)</label>
                <input type="text" wire:model="external_reference" placeholder="..." 
                       class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-rose-500/5 focus:border-rose-500 transition-all font-bold text-gray-700 text-xs">
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Keluar</label>
                <input type="date" wire:model="disbursement_date" 
                       class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-rose-500/5 focus:border-rose-500 transition-all font-bold text-gray-700 text-xs uppercase">
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Distribusi</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" wire:click="$set('status', 'PENDING')"
                            class="py-2 rounded-lg font-black text-[8px] uppercase tracking-widest transition-all border
                            {{ $status == 'PENDING' ? 'bg-gray-100 border-gray-200 text-gray-600' : 'bg-white border-gray-50 text-gray-300' }}">
                        REQUEST
                    </button>
                    <button type="button" wire:click="$set('status', 'COMPLETED')"
                            class="py-2 rounded-lg font-black text-[8px] uppercase tracking-widest transition-all border
                            {{ $status == 'COMPLETED' ? 'bg-green-50 border-green-200 text-green-600' : 'bg-white border-gray-50 text-gray-300' }}">
                        SELESAI
                    </button>
                </div>
            </div>
        </div>

        <!-- SPK GROUPS -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center space-x-2">
                    <div class="w-1 h-4 bg-rose-500 rounded-full"></div>
                    <h2 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Grup SPK Workshop</h2>
                </div>
                <button type="button" wire:click="addSpkGroup" 
                        class="px-3 py-1.5 bg-rose-50 border border-rose-100 text-rose-500 font-black text-[9px] rounded-lg hover:bg-rose-500 hover:text-white transition-all uppercase tracking-widest flex items-center">
                    + GRUP SPK
                </button>
            </div>

            @foreach($spkGroups as $gIndex => $group)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-in fade-in duration-300">
                <div class="px-6 py-3 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-full max-w-[250px]">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-rose-500 font-black text-[10px]">SPK</span>
                                <input type="text" wire:model.live="spkGroups.{{ $gIndex }}.spk_number" list="spks-{{ $gIndex }}" 
                                       placeholder="KETIK NOMOR..."
                                       class="w-full pl-10 pr-4 py-1.5 bg-white border-gray-100 rounded-lg focus:border-rose-500 focus:ring-4 focus:ring-rose-500/5 font-black text-sm text-rose-500 uppercase transition-all">
                                <datalist id="spks-{{ $gIndex }}">
                                    @foreach($allSpks as $spk)
                                        <option value="{{ $spk }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                        <div class="h-6 w-px bg-gray-200"></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 uppercase leading-none">{{ count($group['items']) }} Item</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                wire:click="openMaterialModal({{ $gIndex }})"
                                class="px-4 py-2 bg-rose-500 text-white font-black text-[9px] rounded-lg shadow-sm hover:bg-rose-600 transition-all uppercase tracking-widest">
                            + PILIH MATERIAL
                        </button>
                        
                        @if(count($spkGroups) > 1)
                        <button type="button" wire:click="removeSpkGroup({{ $gIndex }})" class="p-1.5 text-gray-300 hover:text-red-500 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 text-[11px]">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest w-8 text-center">#</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest pl-2">MATERIAL WORKSHOP</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-center w-32">JUMLAH KELUAR</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-right w-40">EST. HARGA</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($group['items'] as $iIndex => $item)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="py-2 text-center font-black text-gray-200">{{ $iIndex + 1 }}</td>
                                <td class="py-2 pl-2">
                                    <p class="font-black text-gray-700 uppercase leading-none">{{ $item['material_name'] }}</p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">STOK: {{ $item['material_stock'] }}</p>
                                </td>
                                <td class="py-2">
                                    <input type="number" wire:model.live="spkGroups.{{ $gIndex }}.items.{{ $iIndex }}.quantity" 
                                           class="w-full max-w-[90px] mx-auto px-2 py-1 bg-white border-gray-100 rounded-md focus:ring-2 focus:ring-rose-500/10 font-black text-xs text-center text-gray-900 transition-all">
                                    @error("spkGroups.{$gIndex}.items.{$iIndex}.quantity") <p class="text-[7px] text-red-500 font-bold mt-1 text-center">OVER</p> @enderror
                                </td>
                                <td class="py-2 text-right font-black text-gray-900">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </td>
                                <td class="py-2 text-center">
                                    <button type="button" wire:click="removeMaterialFromGroup({{ $gIndex }}, {{ $iIndex }})" 
                                            class="p-1 text-gray-200 hover:text-red-500 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Pilih material workshop...</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Sticky Bottom Bar -->
        <div class="fixed bottom-4 right-6 left-auto z-50 w-[calc(100%-300px)] max-w-[1250px] bg-[#22AF85] p-3 rounded-2xl shadow-2xl flex items-center justify-between px-8 border border-white/10">
            <div class="flex items-center gap-10">
                <div class="text-white">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] opacity-60 leading-none">TOTAL ITEM KELUAR</span>
                    @php $iCount = 0; foreach($spkGroups as $g) $iCount += count($g['items']); @endphp
                    <p class="text-2xl font-black tracking-tighter mt-1 leading-none">{{ $iCount }} Material</p>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div>
                    <span class="text-[8px] font-black text-white/60 uppercase tracking-widest block leading-none">STATUS</span>
                    <p class="text-sm font-black text-white mt-1 uppercase">{{ $status }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" 
                        class="px-12 py-3.5 bg-[#FFC232] text-gray-900 font-black rounded-xl shadow-xl hover:scale-[1.03] active:scale-95 transition-all text-center tracking-widest text-xs uppercase">
                    SIMPAN SEMUA DATA
                </button>
            </div>
        </div>
    </form>

    <!-- COMPACT MATERIAL MODAL -->
    @if($showMaterialModal)
    <style>body { overflow: hidden !important; }</style>
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm animate-in fade-in" wire:click="$set('showMaterialModal', false)"></div>
        <div class="relative bg-white w-full max-w-lg max-h-[70vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95">
            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">PILIH MATERIAL OUT</p>
                <button type="button" wire:click="$set('showMaterialModal', false)" class="text-gray-300 hover:text-red-500 transition-all">&times;</button>
            </div>
            <div class="p-4 bg-white border-b border-gray-50">
                <input type="text" wire:model.live="checklistSearch" placeholder="Cari..." 
                       class="w-full px-4 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white font-bold text-xs">
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-1.5 custom-scrollbar">
                @foreach($modalMaterials as $material)
                <button type="button" wire:click="toggleChecklist({{ $material->id }})"
                        class="w-full flex items-center justify-between p-3 rounded-lg border transition-all
                        {{ in_array($material->id, $selectedChecklist) ? 'bg-rose-50 border-rose-500 shadow-sm' : 'bg-white border-gray-100 hover:border-gray-200' }}">
                    <div class="flex items-center text-left">
                        <div class="w-6 h-6 rounded flex items-center justify-center mr-3 {{ in_array($material->id, $selectedChecklist) ? 'bg-rose-500 text-white' : 'bg-gray-100' }}">
                            @if(in_array($material->id, $selectedChecklist)) <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> @endif
                        </div>
                        <div>
                            <span class="font-black text-gray-700 text-[11px] uppercase block leading-tight">{{ $material->name }}</span>
                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">STOK: {{ $material->stock }}</span>
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
            <div class="p-4 border-t border-gray-50 flex items-center justify-between bg-gray-50/30">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ count($selectedChecklist) }} DIPILIH</span>
                <button type="button" wire:click="addFromChecklist" class="px-6 py-2 bg-[#FFC232] text-gray-900 font-black rounded-lg text-[10px] uppercase">TAMBAHKAN</button>
            </div>
        </div>
    </div>
    @endif
</div>
