<div x-data="{ 
    isOpen: false, 
    workOrderId: null,
    category: 'PRODUK_CACAT',
    descUpper: '',
    descSol: '',
    descKondisiBawaan: '',
    
    init() {
        console.log('Report Modal Initialized');
        // Listen to dispatch event
        window.addEventListener('open-report-modal', (e) => {
            console.log('Event received:', e.detail);
            this.workOrderId = e.detail;
            this.isOpen = true;
        });
    },

    close() {
        this.isOpen = false;
        this.descUpper = '';
        this.descSol = '';
        this.descKondisiBawaan = '';
        this.category = 'PRODUK_CACAT';
    }
}"
x-show="isOpen"
style="display: none;"
class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden" 
         @click.away="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        {{-- Header --}}
        <div class="bg-amber-500 p-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Lapor Kendala / Follow Up
            </h3>
            <button @click="close()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">
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

                {{-- Unified Service Input (Recommended & Optional) --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-200" x-data="{ 
                    showDropdown: false, 
                    selected: [], // Array of {formatted: string, type: 'recommended'|'optional'}
                    search: '',
                    price: 0,
                    type: 'recommended',
                    allServices: @js($allServices ?? []),
                    formatPrice(price) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(price);
                    },
                    get filteredServices() {
                        if (!this.search) return this.allServices;
                        return this.allServices.filter(s => 
                            s.name.toLowerCase().includes(this.search.toLowerCase())
                        );
                    },
                    selectService(item) {
                        this.search = item.name;
                        this.price = item.price;
                        this.showDropdown = false;
                    },
                    confirm() {
                        if (this.search.trim() !== '') {
                            const val = this.search.trim() + ' (' + this.formatPrice(this.price) + ')';
                            if (!this.selected.some(s => s.formatted === val)) {
                                this.selected.push({ formatted: val, type: this.type });
                            }
                            this.search = '';
                            this.price = 0;
                            this.showDropdown = false;
                        }
                    },
                    remove(index) {
                        this.selected.splice(index, 1);
                    }
                }">
                    <label class="block text-sm font-black text-gray-700 mb-3 uppercase tracking-wider">Saran Layanan & Perbaikan</label>

                    {{-- Category Toggle --}}
                    <div class="flex gap-2 p-1 bg-white border border-gray-200 rounded-xl w-fit mb-4">
                        <button type="button" @click="type = 'recommended'" 
                            :class="type === 'recommended' ? 'bg-blue-500 text-white shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-1.5">
                            <span x-text="type === 'recommended' ? '💎' : ''"></span>
                            Recommended
                        </button>
                        <button type="button" @click="type = 'optional'" 
                            :class="type === 'optional' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-1.5">
                            <span x-text="type === 'optional' ? '✨' : ''"></span>
                            Optional
                        </button>
                    </div>

                    {{-- Input Area --}}
                    <div class="relative">
                        <div class="flex gap-2">
                            <div @click="showDropdown = !showDropdown" 
                                 class="flex-1 border border-gray-300 rounded-xl p-2.5 min-h-[46px] cursor-pointer bg-white flex flex-wrap gap-2 items-center hover:border-blue-400 transition-all shadow-sm">
                                <template x-for="(item, index) in selected" :key="index">
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1.5 border transition-all"
                                        :class="item.type === 'recommended' 
                                            ? 'bg-blue-50 text-blue-700 border-blue-200' 
                                            : 'bg-amber-50 text-amber-700 border-amber-200'">
                                        <span x-text="item.type === 'recommended' ? '💎' : '✨'"></span>
                                        <span x-text="item.formatted"></span>
                                        <svg @click.stop="remove(index)" class="w-3 h-3 cursor-pointer hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </span>
                                </template>
                                <span x-show="selected.length === 0" class="text-gray-400 text-xs italic ml-1">Pilih jasa yang disarankan...</span>
                            </div>
                        </div>
                        
                        {{-- Hidden Inputs --}}
                        <template x-for="item in selected.filter(i => i.type === 'recommended')">
                            <input type="hidden" name="recommended_services[]" :value="item.formatted">
                        </template>
                        <template x-for="item in selected.filter(i => i.type === 'optional')">
                            <input type="hidden" name="suggested_services[]" :value="item.formatted">
                        </template>

                        <div x-show="showDropdown" @click.away="showDropdown = false"
                             class="absolute z-[9999] mt-2 w-full bg-white border border-gray-200 rounded-2xl shadow-2xl max-h-80 overflow-hidden flex flex-col animate-in fade-in slide-in-from-top-2 duration-200">
                            <div class="p-4 border-b border-gray-100 bg-gray-50 space-y-3">
                                <div class="relative">
                                    <input type="text" x-model="search" 
                                           @keydown.enter.prevent="confirm()"
                                           :placeholder="type === 'recommended' ? 'Cari jasa disarankan...' : 'Cari jasa opsional...'" 
                                           class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 py-2.5 pl-10"
                                           :class="type === 'recommended' ? 'focus:ring-blue-500' : 'focus:ring-amber-500'">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <input type="number" x-model="price" 
                                               @keydown.enter.prevent="confirm()"
                                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 py-2.5 pl-10"
                                               :class="type === 'recommended' ? 'focus:ring-blue-500' : 'focus:ring-amber-500'">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <span class="text-[10px] font-bold text-gray-400">Rp</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="confirm()" 
                                            class="px-5 py-2.5 rounded-xl font-black text-xs transition-all uppercase shadow-sm active:scale-95"
                                            :class="type === 'recommended' ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-amber-500 hover:bg-amber-600 text-white'">
                                        TAMBAH
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-y-auto custom-scrollbar max-h-48 p-2">
                                <template x-for="service in filteredServices" :key="service.id">
                                    <div @click="selectService(service)" 
                                         class="px-4 py-2.5 hover:bg-gray-50 rounded-xl cursor-pointer flex items-center justify-between text-sm transition-colors group">
                                        <span x-text="service.name" class="font-medium text-gray-700 group-hover:text-gray-900"></span>
                                        <span class="text-[10px] font-black" 
                                            :class="type === 'recommended' ? 'text-blue-600' : 'text-amber-600'"
                                            x-text="formatPrice(service.price)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 italic px-1 font-medium">
                        * <span class="text-blue-500 font-bold">Recommended</span> (💎) layanan wajib. <span class="text-amber-500 font-bold">Optional</span> (✨) saran tambahan.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Detail Kendala / Kondisi</label>
                    <div class="space-y-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-[9px] font-black text-gray-400 uppercase">1. Upper</span>
                            </div>
                            <input type="text" name="desc_upper" x-model="descUpper" 
                                placeholder="Detail bagian atas..."
                                class="w-full pl-16 pr-3 py-2 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-[9px] font-black text-gray-400 uppercase">2. Sol</span>
                            </div>
                            <input type="text" name="desc_sol" x-model="descSol" 
                                placeholder="Detail bagian sol..."
                                class="w-full pl-16 pr-3 py-2 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-[9px] font-black text-gray-400 uppercase">3. Kondisi</span>
                            </div>
                            <input type="text" name="desc_kondisi_bawaan" x-model="descKondisiBawaan" 
                                placeholder="Detail kondisi bawaan..."
                                class="w-full pl-20 pr-3 py-2 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                        </div>
                        <input type="hidden" name="description" :value="descUpper + ' | ' + descSol + ' | ' + descKondisiBawaan">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto Bukti (Hanya JPG/PNG)</label>
                    <input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-xs text-gray-500 mt-1">Maksimal 2MB per gambar. Format: JPG, PNG.</p>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="close()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-sm shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
