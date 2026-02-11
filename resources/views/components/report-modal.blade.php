<div x-data="{ 
    isOpen: false, 
    workOrderId: null,
    category: 'PRODUK_CACAT',
    description: '',
    
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
        this.description = '';
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

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Jasa yang Disarankan (Opsional)</label>
                        <div class="relative" x-data="{ 
                        showDropdown: false, 
                        selected: [], 
                        search: '',
                        price: 0,
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
                                if (!this.selected.includes(val)) {
                                    this.selected.push(val);
                                }
                                this.search = '';
                                this.price = 0;
                                this.showDropdown = false;
                            }
                        },
                        toggle(val) {
                            this.selected = this.selected.filter(i => i !== val);
                        }
                    }">
                        <div class="flex gap-2">
                            <div @click="showDropdown = !showDropdown" 
                                 class="flex-1 border border-gray-300 rounded-lg p-2 min-h-[38px] cursor-pointer bg-white flex flex-wrap gap-1 items-center hover:border-amber-400 transition-colors">
                                <template x-for="item in selected" :key="item">
                                    <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                        <span x-text="item"></span>
                                        <svg @click.stop="toggle(item)" class="w-3 h-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </span>
                                </template>
                                <span x-show="selected.length === 0" class="text-gray-400 text-sm italic">Pilih jasa...</span>
                            </div>
                        </div>
                        
                        {{-- Hidden inputs for form submission --}}
                        <template x-for="item in selected" :key="'input-'+item">
                            <input type="hidden" name="suggested_services[]" :value="item">
                        </template>

                        <div x-show="showDropdown" @click.away="showDropdown = false"
                             class="absolute z-[9999] mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-80 overflow-hidden flex flex-col">
                            
                            {{-- Selection Logic (Search & Price) --}}
                            <div class="p-3 border-b border-gray-100 bg-gray-50 space-y-3">
                                <div class="relative">
                                    <input type="text" x-model="search" x-ref="searchInput" 
                                           @keydown.escape="showDropdown = false"
                                           @keydown.enter.prevent="confirm()"
                                           placeholder="Ketik untuk mencari..." 
                                           class="w-full text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-2 pl-8">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <input type="number" x-model="price" 
                                               @keydown.enter.prevent="confirm()"
                                               placeholder="Harga" 
                                               class="w-full text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-2 pl-8">
                                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                            <span class="text-[10px] font-bold text-gray-400">Rp</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="confirm()" 
                                            class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded font-bold text-[10px] transition-colors uppercase">
                                        TAMBAH
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-y-auto custom-scrollbar max-h-48 p-1">
                                <template x-for="service in filteredServices" :key="service.id">
                                    <div @click="selectService(service)" 
                                         class="px-3 py-2 hover:bg-amber-50 rounded cursor-pointer flex items-center justify-between text-sm transition-colors"
                                         :class="selected.some(s => s.startsWith(service.name + ' (')) ? 'bg-amber-50 text-amber-700 font-bold' : 'text-gray-600'">
                                        <span x-text="service.name"></span>
                                        <span class="text-[10px] font-bold text-amber-600" x-text="formatPrice(service.price)"></span>
                                    </div>
                                </template>

                                <div x-show="filteredServices.length === 0 && search.length === 0" class="p-3 text-center text-xs text-gray-400 italic">
                                    Pilih dari list atau ketik jasa baru...
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">* Rekomendasi tindakan teknis untuk tim CX.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi / Catatan</label>
                    <textarea name="description" x-model="description" rows="3" required
                              class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm"
                              placeholder="Jelaskan kendala secara detail..."></textarea>
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
