@php
    $services = \App\Models\Service::orderBy('name')->get()->map(function($service) { return ['name' => data_get($service, 'name'), 'price' => data_get($service, 'price')]; });
@endphp

<div x-data="reportModalData()"
     x-show="isOpen"
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <script>
        function reportModalData() {
            return {
                isOpen: false, 
                workOrderId: null,
                category: 'PRODUK_CACAT',
                descUpper: '',
                descSol: '',
                descKondisiBawaan: '',

                // Structured Services
                services: @json($services),
                
                recService1: '',
                recService1Search: '',
                recService1Price: '',
                recService1Open: false,

                recService2: '',
                recService2Search: '',
                recService2Price: '',
                recService2Open: false,

                sugService1: '',
                sugService1Search: '',
                sugService1Price: '',
                sugService1Open: false,

                sugService2: '',
                sugService2Search: '',
                sugService2Price: '',
                sugService2Open: false,

                updateServiceValue(type, index) {
                    const searchKey = `${type}Service${index}Search`;
                    const priceKey = `${type}Service${index}Price`;
                    const mainKey = `${type}Service${index}`;
                    
                    const name = this[searchKey] || '';
                    const price = this[priceKey] || '0';
                    
                    if (name) {
                        this[mainKey] = `${name} (${price})`;
                    } else {
                        this[mainKey] = '';
                    }
                },

                selectService(type, index, service) {
                    const searchKey = `${type}Service${index}Search`;
                    const priceKey = `${type}Service${index}Price`;
                    const openKey = `${type}Service${index}Open`;
                    
                    this[searchKey] = service.name;
                    this[priceKey] = service.price;
                    this[openKey] = false;
                    this.updateServiceValue(type, index);
                },
                
                init() {
                    window.addEventListener('open-report-modal', (e) => {
                        this.workOrderId = e.detail;
                        this.isOpen = true;
                    });
                },

                close() {
                    this.isOpen = false;
                    this.descUpper = '';
                    this.descSol = '';
                    this.descKondisiBawaan = '';
                    this.recService1 = '';
                    this.recService1Search = '';
                    this.recService1Price = '';
                    this.recService1Open = false;
                    this.recService2 = '';
                    this.recService2Search = '';
                    this.recService2Price = '';
                    this.recService2Open = false;
                    this.sugService1 = '';
                    this.sugService1Search = '';
                    this.sugService1Price = '';
                    this.sugService1Open = false;
                    this.sugService2 = '';
                    this.sugService2Search = '';
                    this.sugService2Price = '';
                    this.sugService2Open = false;
                    this.category = 'PRODUK_CACAT';
                }
            };
        }
    </script>

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh] overflow-hidden" 
         @click.away="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        {{-- Header --}}
        <div class="bg-amber-500 p-4 flex justify-between items-center shrink-0">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Lapor Kendala / Follow Up
            </h3>
            <button @click="close()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Scrollable Body --}}
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <form action="{{ route('cx-issues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="work_order_id" :value="workOrderId">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Masalah</label>
                    <select name="category" x-model="category" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                        <option value="PRODUK_CACAT">Produk Cacat / Rusak</option>
                        <option value="TIDAK_SESUAI_SPK">Tidak Sesuai SPK</option>
                        <option value="MATERIAL_KOSONG">Material Kosong/Habis</option>
                        <option value="BUTUH_INFO_CX">Butuh Konfirmasi Customer (CX)</option>
                        <option value="LAINNYA">Lainnya</option>
                    </select>
                </div>

                {{-- Structured Service Input (Recommended & Optional) --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                    <label class="block text-sm font-black text-gray-700 mb-4 uppercase tracking-wider">Saran Layanan & Perbaikan</label>

                    <div class="space-y-4">
                        {{-- Recommended Services Grid --}}
                        <div class="space-y-2">
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-1">
                                💎 Recommended
                            </span>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {{-- Rec 1 --}}
                                <div class="relative" @click.away="recService1Open = false">
                                    <div class="flex items-stretch shadow-sm group">
                                        <div class="w-14 flex-shrink-0 bg-gray-100 border-y border-l border-gray-200 rounded-l-lg flex items-center px-2">
                                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider text-center leading-tight">R1</span>
                                        </div>
                                        <input type="text" x-model="recService1Search" 
                                            @focus="recService1Open = true"
                                            @input="updateServiceValue('rec', 1)"
                                            placeholder="Jasa..."
                                            class="flex-1 border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-[11px] py-1.5 px-2 border-y border-r-0">
                                        <input type="text" x-model="recService1Price"
                                            @input="updateServiceValue('rec', 1)"
                                            placeholder="0"
                                            class="w-20 border-gray-300 rounded-r-lg focus:ring-blue-500 focus:border-blue-500 text-[11px] py-1.5 px-2 border text-right font-bold text-blue-600">
                                    </div>
                                    <input type="hidden" name="rec_service_1" x-model="recService1">
                                    
                                    <div x-show="recService1Open && services.filter(s => s.name.toLowerCase().includes(recService1Search.toLowerCase())).length > 0"
                                        x-transition
                                        class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-[100] max-h-40 overflow-y-auto">
                                        <template x-for="service in services.filter(s => s.name.toLowerCase().includes(recService1Search.toLowerCase()))">
                                            <div @click="selectService('rec', 1, service)" 
                                                class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0 flex justify-between items-center group">
                                                <span class="text-xs font-bold text-gray-700 group-hover:text-blue-600" x-text="service.name"></span>
                                                <span class="text-[10px] font-black text-blue-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Rec 2 --}}
                                <div class="relative" @click.away="recService2Open = false">
                                    <div class="flex items-stretch shadow-sm group">
                                        <div class="w-14 flex-shrink-0 bg-gray-100 border-y border-l border-gray-200 rounded-l-lg flex items-center px-2">
                                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider text-center leading-tight">R2</span>
                                        </div>
                                        <input type="text" x-model="recService2Search" 
                                            @focus="recService2Open = true"
                                            @input="updateServiceValue('rec', 2)"
                                            placeholder="Jasa..."
                                            class="flex-1 border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-[11px] py-1.5 px-2 border-y border-r-0">
                                        <input type="text" x-model="recService2Price"
                                            @input="updateServiceValue('rec', 2)"
                                            placeholder="0"
                                            class="w-20 border-gray-300 rounded-r-lg focus:ring-blue-500 focus:border-blue-500 text-[11px] py-1.5 px-2 border text-right font-bold text-blue-600">
                                    </div>
                                    <input type="hidden" name="rec_service_2" x-model="recService2">
                                    
                                    <div x-show="recService2Open && services.filter(s => s.name.toLowerCase().includes(recService2Search.toLowerCase())).length > 0"
                                        x-transition
                                        class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-[100] max-h-40 overflow-y-auto">
                                        <template x-for="service in services.filter(s => s.name.toLowerCase().includes(recService2Search.toLowerCase()))">
                                            <div @click="selectService('rec', 2, service)" 
                                                class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0 flex justify-between items-center group">
                                                <span class="text-xs font-bold text-gray-700 group-hover:text-blue-600" x-text="service.name"></span>
                                                <span class="text-[10px] font-black text-blue-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Optional Services Grid --}}
                        <div class="space-y-2">
                            <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest flex items-center gap-1">
                                ✨ Optional
                            </span>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {{-- Opt 1 --}}
                                <div class="relative" @click.away="sugService1Open = false">
                                    <div class="flex items-stretch shadow-sm group">
                                        <div class="w-14 flex-shrink-0 bg-gray-100 border-y border-l border-gray-200 rounded-l-lg flex items-center px-2">
                                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider text-center leading-tight">O1</span>
                                        </div>
                                        <input type="text" x-model="sugService1Search" 
                                            @focus="sugService1Open = true"
                                            @input="updateServiceValue('sug', 1)"
                                            placeholder="Jasa..."
                                            class="flex-1 border-gray-300 focus:ring-amber-500 focus:border-amber-500 text-[11px] py-1.5 px-2 border-y border-r-0">
                                        <input type="text" x-model="sugService1Price"
                                            @input="updateServiceValue('sug', 1)"
                                            placeholder="0"
                                            class="w-20 border-gray-300 rounded-r-lg focus:ring-amber-500 focus:border-amber-500 text-[11px] py-1.5 px-2 border text-right font-bold text-amber-600">
                                    </div>
                                    <input type="hidden" name="sug_service_1" x-model="sugService1">
                                    
                                    <div x-show="sugService1Open && services.filter(s => s.name.toLowerCase().includes(sugService1Search.toLowerCase())).length > 0"
                                        x-transition
                                        class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-[100] max-h-40 overflow-y-auto">
                                        <template x-for="service in services.filter(s => s.name.toLowerCase().includes(sugService1Search.toLowerCase()))">
                                            <div @click="selectService('sug', 1, service)" 
                                                class="px-3 py-2 hover:bg-amber-50 cursor-pointer border-b border-gray-50 last:border-0 flex justify-between items-center group">
                                                <span class="text-xs font-bold text-gray-700 group-hover:text-amber-600" x-text="service.name"></span>
                                                <span class="text-[10px] font-black text-amber-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Opt 2 --}}
                                <div class="relative" @click.away="sugService2Open = false">
                                    <div class="flex items-stretch shadow-sm group">
                                        <div class="w-14 flex-shrink-0 bg-gray-100 border-y border-l border-gray-200 rounded-l-lg flex items-center px-2">
                                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider text-center leading-tight">O2</span>
                                        </div>
                                        <input type="text" x-model="sugService2Search" 
                                            @focus="sugService2Open = true"
                                            @input="updateServiceValue('sug', 2)"
                                            placeholder="Jasa..."
                                            class="flex-1 border-gray-300 focus:ring-amber-500 focus:border-amber-500 text-[11px] py-1.5 px-2 border-y border-r-0">
                                        <input type="text" x-model="sugService2Price"
                                            @input="updateServiceValue('sug', 2)"
                                            placeholder="0"
                                            class="w-20 border-gray-300 rounded-r-lg focus:ring-amber-500 focus:border-amber-500 text-[11px] py-1.5 px-2 border text-right font-bold text-amber-600">
                                    </div>
                                    <input type="hidden" name="sug_service_2" x-model="sugService2">
                                    
                                    <div x-show="sugService2Open && services.filter(s => s.name.toLowerCase().includes(sugService2Search.toLowerCase())).length > 0"
                                        x-transition
                                        class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-[100] max-h-40 overflow-y-auto">
                                        <template x-for="service in services.filter(s => s.name.toLowerCase().includes(sugService2Search.toLowerCase()))">
                                            <div @click="selectService('sug', 2, service)" 
                                                class="px-3 py-2 hover:bg-amber-50 cursor-pointer border-b border-gray-50 last:border-0 flex justify-between items-center group">
                                                <span class="text-xs font-bold text-gray-700 group-hover:text-amber-600" x-text="service.name"></span>
                                                <span class="text-[10px] font-black text-amber-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Backwards Compatibility for Old Columns --}}
                    <template x-if="recService1">
                        <input type="hidden" name="recommended_services[]" :value="recService1">
                    </template>
                    <template x-if="recService2">
                        <input type="hidden" name="recommended_services[]" :value="recService2">
                    </template>
                    <template x-if="sugService1">
                        <input type="hidden" name="suggested_services[]" :value="sugService1">
                    </template>
                    <template x-if="sugService2">
                        <input type="hidden" name="suggested_services[]" :value="sugService2">
                    </template>

                    <p class="text-[9px] text-gray-400 mt-2 italic px-1 font-medium leading-tight">
                        * Input-kan saran perbaikan yang disarankan untuk customer.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Detail Kendala / Kondisi</label>
                    <div class="space-y-2">
                        <div class="flex items-stretch">
                            <div class="w-20 flex-shrink-0 bg-gray-100 border-y border-l border-gray-200 rounded-l-lg flex items-center px-2">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">1. Upper</span>
                            </div>
                            <input type="text" name="desc_upper" x-model="descUpper" 
                                placeholder="Detail bagian atas..."
                                class="flex-1 border-gray-300 rounded-r-lg focus:ring-amber-500 focus:border-amber-500 text-[11px] py-1.5 px-2 border">
                        </div>

                        <div class="flex items-stretch">
                            <div class="w-20 flex-shrink-0 bg-gray-100 border-y border-l border-gray-200 rounded-l-lg flex items-center px-2">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">2. Sol</span>
                            </div>
                            <input type="text" name="desc_sol" x-model="descSol" 
                                placeholder="Detail bagian sol..."
                                class="flex-1 border-gray-300 rounded-r-lg focus:ring-amber-500 focus:border-amber-500 text-[11px] py-1.5 px-2 border">
                        </div>

                        <div class="flex items-stretch">
                            <div class="w-20 flex-shrink-0 bg-gray-100 border-y border-l border-gray-200 rounded-l-lg flex items-center px-2">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">3. Kondisi</span>
                            </div>
                            <input type="text" name="desc_kondisi_bawaan" x-model="descKondisiBawaan" 
                                placeholder="Detail kondisi bawaan..."
                                class="flex-1 border-gray-300 rounded-r-lg focus:ring-amber-500 focus:border-amber-500 text-[11px] py-1.5 px-2 border">
                        </div>
                        <input type="hidden" name="description" :value="(descUpper || '-') + ' | ' + (descSol || '-') + ' | ' + (descKondisiBawaan || '-')">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto Bukti (Hanya JPG/PNG)</label>
                    <input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[11px] file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-[10px] text-gray-500 mt-1">Maksimal 2MB per gambar. Format: JPG, PNG.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 sticky bottom-0 bg-white pb-2">
                    <button type="button" @click="close()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-xs shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
