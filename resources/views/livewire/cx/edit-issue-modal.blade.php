<div>
    <div x-data="{ show: @entangle('showEditModal') }" x-show="show" 
        class="fixed inset-0 z-[200] overflow-y-auto" x-cloak>
        
        <style>
            [x-cloak] { display: none !important; }
            .modal-glass {
                background: rgba(17, 24, 39, 0.8);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
            }
            .custom-scrollbar::-webkit-scrollbar { width: 5px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: rgba(31, 41, 55, 0.5); }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(75, 85, 99, 0.5); border-radius: 10px; }
        </style>

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Backdrop --}}
            <div x-show="show" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black/90 transition-opacity" aria-hidden="true" @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Content --}}
            <div x-show="show" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                 class="inline-block align-middle bg-gray-900 rounded-[2rem] text-left shadow-[0_0_50px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-gray-800 relative z-[210] overflow-hidden">
                
                {{-- Header --}}
                <div class="px-8 py-6 border-b border-gray-800 flex justify-between items-center bg-gray-900/50">
                    <div>
                        <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase leading-none">Elite Edit Issue</h3>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 rounded-full text-[10px] font-black text-emerald-400 uppercase tracking-widest italic">
                                SPK: {{ $spk_number ?: '-' }}
                            </span>
                            <span class="px-3 py-1 bg-blue-500/10 border border-blue-500/30 rounded-full text-[10px] font-black text-blue-400 uppercase tracking-widest italic">
                                {{ $category }}
                            </span>
                        </div>
                    </div>
                    <button @click="show = false" class="text-gray-500 hover:text-white hover:bg-gray-800 p-2 rounded-xl transition-all">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="px-8 py-8 space-y-8 bg-gray-900/80 custom-scrollbar max-h-[75vh] overflow-y-auto">
                    
                    {{-- Detail Kendala & Solusi --}}
                    @if(in_array($category, ['TEKNIS', 'MATERIAL', 'KONFIRMASI']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kendala Section --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-amber-500 uppercase tracking-widest ml-1 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Detail Kendala
                            </label>
                            
                            {{-- Dropdown Kendala 1 --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button" class="w-full bg-gray-800/50 border border-gray-700 rounded-2xl px-5 py-4 text-left text-sm font-bold text-white hover:border-amber-500/50 transition-all flex justify-between items-center">
                                    <span class="truncate">{{ $kendala_1 ?: '-- Pilih Kendala 1 --' }}</span>
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute z-[300] w-full mt-2 bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl overflow-hidden p-1">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                        @foreach($masterIssues as $item)
                                            <button wire:click="selectOption('kendala_1', '{{ $item['name'] }}'); open = false" @click="open = false" class="w-full text-left px-4 py-3 hover:bg-amber-500/10 text-gray-300 text-sm font-medium transition-all border-b border-gray-700/50">{{ $item['name'] }}</button>
                                        @endforeach
                                        <button wire:click="selectOption('kendala_1', 'Lainnya'); open = false" @click="open = false" class="w-full text-left px-4 py-3 bg-amber-600/10 text-amber-500 text-xs font-black uppercase italic tracking-widest">✏️ Ketik Manual...</button>
                                    </div>
                                </div>
                            </div>
                            @if($is_k1_manual)
                                <input type="text" wire:model.live="kendala_1" placeholder="Isi kendala manual..." class="w-full bg-gray-900 border-amber-500/50 border-2 rounded-2xl px-5 py-4 text-sm text-white font-bold focus:ring-amber-500 animate-in slide-in-from-top-1">
                            @endif

                            {{-- Dropdown Kendala 2 --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button" class="w-full bg-gray-800/50 border border-gray-700 rounded-2xl px-5 py-4 text-left text-sm font-bold text-white hover:border-amber-500/50 transition-all flex justify-between items-center">
                                    <span class="truncate">{{ $kendala_2 ?: '-- Pilih Kendala 2 (Opsional) --' }}</span>
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute z-[300] w-full mt-2 bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl overflow-hidden p-1">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                        @foreach($masterIssues as $item)
                                            <button wire:click="selectOption('kendala_2', '{{ $item['name'] }}'); open = false" @click="open = false" class="w-full text-left px-4 py-3 hover:bg-amber-500/10 text-gray-300 text-sm font-medium transition-all border-b border-gray-700/50">{{ $item['name'] }}</button>
                                        @endforeach
                                        <button wire:click="selectOption('kendala_2', 'Lainnya'); open = false" @click="open = false" class="w-full text-left px-4 py-3 bg-amber-600/10 text-amber-500 text-xs font-black uppercase italic tracking-widest">✏️ Ketik Manual...</button>
                                    </div>
                                </div>
                            </div>
                            @if($is_k2_manual)
                                <input type="text" wire:model.live="kendala_2" placeholder="Isi kendala 2 manual..." class="w-full bg-gray-900 border-amber-500/50 border-2 rounded-2xl px-5 py-4 text-sm text-white font-bold focus:ring-amber-500 animate-in slide-in-from-top-1">
                            @endif
                        </div>

                        {{-- Solusi Section --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-emerald-500 uppercase tracking-widest ml-1 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Opsi Solusi
                            </label>

                            {{-- Dropdown Solusi 1 --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button" class="w-full bg-gray-800/50 border border-gray-700 rounded-2xl px-5 py-4 text-left text-sm font-bold text-white hover:border-emerald-500/50 transition-all flex justify-between items-center">
                                    <span class="truncate">{{ $opsi_solusi_1 ?: '-- Pilih Solusi 1 --' }}</span>
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute z-[300] w-full mt-2 bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl overflow-hidden p-1">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                        @foreach($masterSolutions as $item)
                                            <button wire:click="selectOption('opsi_solusi_1', '{{ $item['name'] }}'); open = false" @click="open = false" class="w-full text-left px-4 py-3 hover:bg-emerald-500/10 text-gray-300 text-sm font-medium transition-all border-b border-gray-700/50">{{ $item['name'] }}</button>
                                        @endforeach
                                        <button wire:click="selectOption('opsi_solusi_1', 'Lainnya'); open = false" @click="open = false" class="w-full text-left px-4 py-3 bg-emerald-600/10 text-emerald-500 text-xs font-black uppercase italic tracking-widest">✏️ Ketik Manual...</button>
                                    </div>
                                </div>
                            </div>
                            @if($is_os1_manual)
                                <input type="text" wire:model.live="opsi_solusi_1" placeholder="Isi solusi manual..." class="w-full bg-gray-900 border-emerald-500/50 border-2 rounded-2xl px-5 py-4 text-sm text-white font-bold focus:ring-emerald-500 animate-in slide-in-from-top-1">
                            @endif

                            {{-- Dropdown Solusi 2 --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button" class="w-full bg-gray-800/50 border border-gray-700 rounded-2xl px-5 py-4 text-left text-sm font-bold text-white hover:border-emerald-500/50 transition-all flex justify-between items-center">
                                    <span class="truncate">{{ $opsi_solusi_2 ?: '-- Pilih Solusi 2 (Opsional) --' }}</span>
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute z-[300] w-full mt-2 bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl overflow-hidden p-1">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                        @foreach($masterSolutions as $item)
                                            <button wire:click="selectOption('opsi_solusi_2', '{{ $item['name'] }}'); open = false" @click="open = false" class="w-full text-left px-4 py-3 hover:bg-emerald-500/10 text-gray-300 text-sm font-medium transition-all border-b border-gray-700/50">{{ $item['name'] }}</button>
                                        @endforeach
                                        <button wire:click="selectOption('opsi_solusi_2', 'Lainnya'); open = false" @click="open = false" class="w-full text-left px-4 py-3 bg-emerald-600/10 text-emerald-500 text-xs font-black uppercase italic tracking-widest">✏️ Ketik Manual...</button>
                                    </div>
                                </div>
                            </div>
                            @if($is_os2_manual)
                                <input type="text" wire:model.live="opsi_solusi_2" placeholder="Isi solusi 2 manual..." class="w-full bg-gray-900 border-emerald-500/50 border-2 rounded-2xl px-5 py-4 text-sm text-white font-bold focus:ring-emerald-500 animate-in slide-in-from-top-1">
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Estimasi Waktu Tambahan Field --}}
                    @if(in_array($category, ['TEKNIS', 'MATERIAL', 'KONFIRMASI']))
                    <div class="space-y-3 p-4 bg-amber-500/10 border border-amber-500/30 rounded-2xl">
                        <label class="text-[10px] font-black text-amber-400 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Estimasi Waktu Tambahan (Opsional)
                        </label>
                        <input type="text" wire:model="estimasi_tambahan" placeholder="Contoh: 3 HARI..." class="w-full bg-gray-800 border-gray-700 rounded-xl px-4 py-3 text-sm text-white font-bold focus:ring-amber-500">
                    </div>
                    @endif

                    {{-- Description Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Keterangan Detail (Atas)</label>
                            <textarea wire:model="desc_upper" rows="3" class="w-full bg-gray-800/30 border border-gray-700 rounded-2xl p-4 text-white text-sm focus:ring-blue-500 transition-all shadow-inner custom-scrollbar" placeholder="Detail tambahan..."></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Keterangan Solusi (Bawah)</label>
                            <textarea wire:model="desc_sol" rows="3" class="w-full bg-gray-800/30 border border-gray-700 rounded-2xl p-4 text-white text-sm focus:ring-blue-500 transition-all shadow-inner custom-scrollbar" placeholder="Detail teknis solusi..."></textarea>
                        </div>
                    </div>

                    {{-- Photo Section --}}
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-teal-500 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span> Dokumentasi Media
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                            @foreach($existingPhotoUrls as $index => $url)
                            <div class="group relative aspect-square rounded-2xl overflow-hidden border-2 border-gray-800 bg-gray-800 shadow-xl">
                                <img src="{{ $url }}" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500">
                                <button type="button" wire:click="removeExistingPhoto({{ $index }})" class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                            @endforeach

                            @foreach($newPhotos as $index => $photo)
                            <div class="group relative aspect-square rounded-2xl overflow-hidden border-2 border-teal-500/30 bg-teal-900/10 shadow-xl">
                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                <div class="absolute top-2 left-2 bg-emerald-500 text-[8px] font-black px-1.5 py-0.5 rounded text-white shadow-lg uppercase tracking-tighter">New</div>
                                <button type="button" wire:click="removeNewPhoto({{ $index }})" class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                            @endforeach

                            <label class="aspect-square rounded-2xl border-2 border-dashed border-gray-700 hover:border-teal-500 hover:bg-teal-500/5 transition-all flex flex-col items-center justify-center cursor-pointer group relative overflow-hidden">
                                <div wire:loading wire:target="photosToUpload" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
                                    <svg class="animate-spin h-6 w-6 text-teal-500 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-[8px] font-black text-teal-500 uppercase tracking-widest">Uploading...</span>
                                </div>
                                <svg class="w-6 h-6 text-gray-600 group-hover:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>
                                <span class="text-[8px] font-black text-gray-500 uppercase mt-1">Tambah</span>
                                <input type="file" wire:model="photosToUpload" multiple class="hidden" accept="image/*">
                            </label>
                        </div>
                    </div>

                    {{-- Saran Layanan & Jasa --}}
                    <div class="grid grid-cols-1 gap-8">
                        {{-- Recommended Section --}}
                        <div class="space-y-6 p-8 bg-blue-500/5 rounded-[2.5rem] border border-blue-500/10 shadow-inner">
                            <label class="block text-xs font-black text-blue-400 uppercase tracking-[0.3em] italic flex items-center gap-3 ml-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                                🚀 Saran Jasa Wajib (Recommended)
                            </label>
                            
                            <div class="space-y-4">
                                @foreach(['recService1', 'recService2'] as $sName)
                                <div class="space-y-2">
                                    <div class="flex gap-3">
                                        <div class="w-32 flex-shrink-0">
                                            <select wire:model.live="{{ $sName }}Category" class="w-full bg-gray-900 border-gray-700 rounded-2xl px-4 py-4 text-xs font-black text-gray-400 focus:ring-blue-500 transition-all">
                                                <option value="">Kategori</option>
                                                @foreach($categories as $cat) <option value="{{ $cat }}">{{ $cat }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div x-data="{ open: false }" class="flex-1 relative">
                                            <input type="text" wire:model.live="{{ $sName }}Search" @focus="open = true" placeholder="Ketik nama jasa di sini..." class="w-full bg-gray-900 border-gray-700 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-blue-500 transition-all">
                                            
                                            @php 
                                                $catVal = $this->{$sName . 'Category'};
                                                $searchVal = $this->{$sName . 'Search'};
                                                $filtered = $this->getFilteredServices($catVal, $searchVal); 
                                            @endphp
                                            
                                            @if($catVal && count($filtered) > 0)
                                            <div x-show="open" @click.away="open = false" class="absolute z-[500] w-full mt-2 bg-gray-800 border border-gray-700 rounded-2xl shadow-[0_30px_60px_rgba(0,0,0,0.8)] overflow-hidden">
                                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                                    @foreach($filtered as $s)
                                                        <button type="button" wire:click="selectService('{{ substr($sName, 0, 3) }}', {{ substr($sName, -1) }}, '{{ $s->name }}', {{ $s->price }})" @click="open = false" class="w-full text-left px-6 py-4 hover:bg-blue-600/20 text-gray-200 text-sm border-b border-gray-700/30 transition-all">
                                                            <div class="flex justify-between items-center">
                                                                <span class="font-bold">{{ $s->name }}</span>
                                                                <span class="text-blue-400 font-black">Rp{{ number_format($s->price) }}</span>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="w-48 flex-shrink-0">
                                            <div class="relative">
                                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-blue-500/50">RP</span>
                                                <input type="number" wire:model.live="{{ $sName }}Price" placeholder="Harga" class="w-full bg-gray-900 border-gray-700 rounded-2xl pl-10 pr-4 py-4 text-sm font-black text-blue-400 focus:ring-blue-500 transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Suggested Section --}}
                        <div class="space-y-6 p-8 bg-amber-500/5 rounded-[2.5rem] border border-amber-500/10 shadow-inner">
                            <label class="block text-xs font-black text-amber-500 uppercase tracking-[0.3em] italic flex items-center gap-3 ml-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                💡 Opsi Jasa Tambahan (Suggested)
                            </label>
                            
                            <div class="space-y-4">
                                @foreach(['sugService1', 'sugService2'] as $sName)
                                <div class="space-y-2">
                                    <div class="flex gap-3">
                                        <div class="w-32 flex-shrink-0">
                                            <select wire:model.live="{{ $sName }}Category" class="w-full bg-gray-900 border-gray-700 rounded-2xl px-4 py-4 text-xs font-black text-gray-400 focus:ring-amber-500 transition-all">
                                                <option value="">Kategori</option>
                                                @foreach($categories as $cat) <option value="{{ $cat }}">{{ $cat }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div x-data="{ open: false }" class="flex-1 relative">
                                            <input type="text" wire:model.live="{{ $sName }}Search" @focus="open = true" placeholder="Ketik nama jasa di sini..." class="w-full bg-gray-900 border-gray-700 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-amber-500 transition-all">
                                            @php 
                                                $catVal = $this->{$sName . 'Category'};
                                                $searchVal = $this->{$sName . 'Search'};
                                                $filtered = $this->getFilteredServices($catVal, $searchVal); 
                                            @endphp
                                            @if($catVal && count($filtered) > 0)
                                            <div x-show="open" @click.away="open = false" class="absolute z-[500] w-full mt-2 bg-gray-800 border border-gray-700 rounded-2xl shadow-[0_30px_60px_rgba(0,0,0,0.8)] overflow-hidden">
                                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                                    @foreach($filtered as $s)
                                                        <button type="button" wire:click="selectService('{{ substr($sName, 0, 3) }}', {{ substr($sName, -1) }}, '{{ $s->name }}', {{ $s->price }})" @click="open = false" class="w-full text-left px-6 py-4 hover:bg-amber-600/20 text-gray-200 text-sm border-b border-gray-700/30 transition-all">
                                                            <div class="flex justify-between items-center">
                                                                <span class="font-bold">{{ $s->name }}</span>
                                                                <span class="text-amber-500 font-black">Rp{{ number_format($s->price) }}</span>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="w-48 flex-shrink-0">
                                            <div class="relative">
                                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-amber-500/50">RP</span>
                                                <input type="number" wire:model.live="{{ $sName }}Price" placeholder="Harga" class="w-full bg-gray-900 border-gray-700 rounded-2xl pl-10 pr-4 py-4 text-sm font-black text-amber-500 focus:ring-amber-500 transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-6 border-t border-gray-800 flex justify-end gap-4 bg-gray-900">
                    <button @click="show = false" class="px-6 py-3 text-sm font-black text-gray-500 hover:text-white transition-all uppercase">Batal</button>
                    <button wire:click="save" wire:loading.attr="disabled" class="px-10 py-3 bg-gradient-to-r from-teal-600 to-blue-700 text-white rounded-2xl text-sm font-black uppercase tracking-widest shadow-xl hover:shadow-teal-500/20 active:scale-95 transition-all flex items-center gap-2">
                        <span wire:loading.remove>Update Data</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
