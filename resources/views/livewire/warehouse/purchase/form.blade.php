<div class="p-6 space-y-4 bg-[#F8F9FA] min-h-screen relative font-sans">
    <!-- Header: Ultra Slim -->
    <div class="flex items-center justify-between max-w-[1600px] mx-auto">
        <div class="flex items-center space-x-3">
            <a href="{{ route('storage.purchase.index') }}" class="p-1.5 bg-white rounded-lg shadow-sm border border-gray-100 text-gray-400 hover:text-[#22AF85] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 leading-none tracking-tight">{{ $purchaseId ? 'EDIT' : 'BARU' }} <span class="text-[#22AF85]">BELANJA</span></h1>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">GUDANG / MANAJEMEN SPK</p>
            </div>
        </div>
        
        <div class="bg-white px-4 py-1.5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="text-right">
                <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest block leading-none">SYSTEM ID</span>
                <p class="text-[11px] font-black text-[#22AF85] leading-none mt-1">{{ $purchase_number }}</p>
            </div>
            <div class="w-px h-6 bg-gray-100"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full {{ $status === 'COMPLETED' ? 'bg-[#22AF85]' : 'bg-[#FFC232]' }}"></div>
                <span class="text-[9px] font-black text-gray-900 uppercase tracking-widest">{{ $status }}</span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-4 max-w-[1600px] mx-auto pb-24">
        <!-- Main Form Data -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Nota Vendor</label>
                <input type="text" wire:model="external_reference" placeholder="..." 
                       class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all font-bold text-gray-700 text-xs">
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal</label>
                <input type="date" wire:model="purchase_date" 
                       class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] transition-all font-bold text-gray-700 text-xs uppercase">
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Prioritas</label>
                <select wire:model="purchase_type" class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 focus:border-[#22AF85] font-black text-gray-700 text-xs">
                    <option value="Reguler">Reguler</option>
                    <option value="Prioritas">Prioritas</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                <select wire:model.live="status" 
                        class="w-full px-3 py-2 bg-gray-50 border-gray-100 rounded-lg focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-xs
                        {{ $status === 'COMPLETED' ? 'text-[#22AF85]' : ($status === 'CANCELLED' ? 'text-red-500' : 'text-gray-500') }}">
                    <option value="PENDING">PENDING</option>
                    <option value="PROCESSING">PROCESSING</option>
                    <option value="COMPLETED">COMPLETED</option>
                    <option value="CANCELLED">CANCELLED</option>
                </select>
            </div>
        </div>

        <!-- SPK GROUPS -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center space-x-2">
                    <div class="w-1 h-4 bg-[#FFC232] rounded-full"></div>
                    <h2 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Grup SPK</h2>
                </div>
                <button type="button" wire:click="addSpkGroup" 
                        class="px-3 py-1.5 bg-[#22AF85]/5 border border-[#22AF85]/20 text-[#22AF85] font-black text-[9px] rounded-lg hover:bg-[#22AF85] hover:text-white transition-all uppercase tracking-widest flex items-center">
                    + GRUP SPK
                </button>
            </div>

            @foreach($spkGroups as $gIndex => $group)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-in fade-in duration-300">
                <div class="px-6 py-3 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-full max-w-[250px]">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#22AF85] font-black text-[10px]">SPK</span>
                                <input type="text" wire:model.live="spkGroups.{{ $gIndex }}.spk_number" list="spks-{{ $gIndex }}" 
                                       placeholder="KETIK NOMOR..."
                                       class="w-full pl-10 pr-4 py-1.5 bg-white border-gray-100 rounded-lg focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/5 font-black text-sm text-[#22AF85] uppercase transition-all">
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
                                class="px-4 py-2 bg-[#22AF85] text-white font-black text-[9px] rounded-lg shadow-sm hover:bg-[#1b8c6a] transition-all uppercase tracking-widest">
                            + PILIH MATERIAL
                        </button>
                        
                        @if(count($spkGroups) > 1)
                        <button type="button" wire:click="removeSpkGroup({{ $gIndex }})" class="p-1.5 text-gray-300 hover:text-red-500 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest w-8 text-center">#</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest pl-2">MATERIAL</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-center w-24">QTY</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-right w-40">HARGA</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest text-right w-40">SUBTOTAL</th>
                                <th class="pb-2 text-[8px] font-black text-gray-300 uppercase tracking-widest w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($group['items'] as $iIndex => $item)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="py-2.5 text-center text-[10px] font-black text-gray-200">{{ $iIndex + 1 }}</td>
                                <td class="py-2.5 pl-2">
                                    <p class="text-xs font-black text-gray-800 uppercase tracking-tight">{{ $item['material_name'] }}</p>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        @if(!empty($item['material_type']))
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase">
                                                🏷️ {{ $item['material_type'] }}
                                            </span>
                                        @endif
                                        @if(!empty($item['material_size']))
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-purple-50 text-purple-600 border border-purple-100 uppercase">
                                                📏 Ukuran: {{ $item['material_size'] }}
                                            </span>
                                        @endif
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-gray-50 text-gray-500 border border-gray-100 uppercase">
                                            STOK: {{ $item['material_stock'] ?? 0 }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-2.5 text-center">
                                    <input type="number" wire:model.live="spkGroups.{{ $gIndex }}.items.{{ $iIndex }}.quantity" 
                                           class="w-full max-w-[80px] mx-auto px-2 py-1 bg-white border-gray-100 rounded-md focus:ring-2 focus:ring-[#22AF85]/10 font-black text-xs text-center text-gray-900 transition-all">
                                </td>
                                <td class="py-2.5 text-right">
                                    <div class="relative max-w-[140px] ml-auto">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-300 font-bold text-[8px]">Rp</span>
                                        <input type="number" wire:model.live="spkGroups.{{ $gIndex }}.items.{{ $iIndex }}.price" 
                                               class="w-full pl-6 pr-2 py-1 bg-white border-gray-100 rounded-md focus:ring-2 focus:ring-[#22AF85]/10 font-black text-xs text-right text-gray-900 transition-all">
                                    </div>
                                </td>
                                <td class="py-2.5 text-right text-xs font-black text-gray-900">
                                    Rp {{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 text-center">
                                    <button type="button" wire:click="removeMaterialFromGroup({{ $gIndex }}, {{ $iIndex }})" 
                                            class="p-1 text-gray-200 hover:text-red-500 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Belum ada material</td>
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
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] opacity-60 leading-none">GRAND TOTAL</span>
                    @php 
                        $grandTotal = 0;
                        foreach($spkGroups as $g) foreach($g['items'] as $i) $grandTotal += ($i['quantity'] * $i['price']);
                    @endphp
                    <p class="text-2xl font-black tracking-tighter mt-1 leading-none">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="flex items-center gap-6 text-white/80">
                    <div class="text-center">
                        <span class="text-[8px] font-black uppercase tracking-widest block opacity-60">GRUP</span>
                        <p class="text-sm font-black">{{ count($spkGroups) }}</p>
                    </div>
                    <div class="text-center">
                        <span class="text-[8px] font-black uppercase tracking-widest block opacity-60">ITEM</span>
                        @php $iCount = 0; foreach($spkGroups as $g) $iCount += count($g['items']); @endphp
                        <p class="text-sm font-black">{{ $iCount }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" 
                        class="px-10 py-3.5 bg-[#FFC232] text-gray-900 font-black rounded-xl shadow-xl hover:scale-[1.03] active:scale-95 transition-all text-center tracking-widest text-xs uppercase">
                    {{ $purchaseId ? 'PERBARUI' : 'SIMPAN' }}
                </button>
            </div>
        </div>
    </form>

    <!-- MATERIAL SELECTION MODAL -->
    @if($showMaterialModal)
    <style>body { overflow: hidden !important; }</style>
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm animate-in fade-in" wire:click="$set('showMaterialModal', false)"></div>
        <div class="relative bg-white w-full max-w-2xl max-h-[85vh] rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 border border-gray-100">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-teal-600 to-emerald-700 text-white">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/10 rounded-xl">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider">PILIH MATERIAL BELANJA</h3>
                        <p class="text-[10px] text-teal-100 mt-0.5">Pilih material berdasarkan Nama, Tipe, Size, dan Stok</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('showMaterialModal', false)" class="p-1.5 rounded-xl hover:bg-white/20 text-white transition-all">&times;</button>
            </div>

            <!-- Search Field -->
            <div class="p-4 bg-gray-50/50 border-b border-gray-100">
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" wire:model.live.debounce.150ms="checklistSearch" 
                           placeholder="Cari Nama Material, Tipe, atau Ukuran (cth: Sol, Vans, 40)..." 
                           class="w-full pl-10 pr-10 py-2.5 bg-white border-gray-200 rounded-xl focus:border-[#22AF85] focus:ring-4 focus:ring-[#22AF85]/10 font-bold text-xs text-gray-800 shadow-sm transition-all">
                    @if($checklistSearch)
                    <button type="button" wire:click="$set('checklistSearch', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Material Items List -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2.5 custom-scrollbar">
                @forelse($modalMaterials as $material)
                @php
                    $isSelected = in_array($material->id, $selectedChecklist);
                @endphp
                <div wire:click="toggleChecklist({{ $material->id }})"
                     class="cursor-pointer group flex items-center justify-between p-3.5 rounded-2xl border transition-all duration-200
                     {{ $isSelected ? 'bg-[#22AF85]/5 border-[#22AF85] shadow-md' : 'bg-white border-gray-100 hover:border-teal-200 hover:bg-teal-50/20' }}">
                    
                    <div class="flex items-center space-x-3.5 min-w-0">
                        <!-- Custom Checkbox Icon -->
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center transition-all flex-shrink-0
                                    {{ $isSelected ? 'bg-[#22AF85] text-white shadow-sm scale-105' : 'bg-gray-100 group-hover:bg-gray-200 text-transparent' }}">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                        </div>

                        <!-- Material Info -->
                        <div class="min-w-0">
                            <p class="font-black text-gray-900 text-xs uppercase tracking-tight truncate">{{ $material->name }}</p>
                            
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                <!-- Type Badge -->
                                @if($material->type)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                        🏷️ {{ $material->type }}
                                    </span>
                                @endif

                                <!-- Size Badge -->
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold {{ $material->size ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-gray-50 text-gray-400 border border-gray-100' }} uppercase">
                                    📏 {{ $material->size ? 'Ukuran: ' . $material->size : 'Tanpa Size' }}
                                </span>

                                <!-- Price Badge -->
                                @if($material->price > 0)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-gray-50 text-gray-600 border border-gray-100">
                                        Rp {{ number_format($material->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Stock Badge -->
                    <div class="ml-4 flex-shrink-0 text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                    {{ $material->stock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $material->stock > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            Stok: {{ $material->stock }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-40 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5"/></svg>
                    <p class="font-bold text-xs">Material tidak ditemukan.</p>
                </div>
                @endforelse
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ count($selectedChecklist) }} MATERIAL DIPILIH</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" wire:click="$set('showMaterialModal', false)" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl text-xs hover:bg-gray-200 transition-all">BATAL</button>
                    <button type="button" wire:click="addFromChecklist" 
                            class="px-6 py-2 bg-[#FFC232] text-gray-900 font-black rounded-xl text-xs uppercase tracking-wider shadow-md hover:scale-105 active:scale-95 transition-all">
                        TAMBAHKAN {{ count($selectedChecklist) > 0 ? '(' . count($selectedChecklist) . ')' : '' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
