{{-- Modal: Create Quotation (Multi-Item Data Barang) --}}
<div id="quotationModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden" 
         x-data="quotationManager()">
        {{-- Modal Header --}}
        <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Draft Quotation</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Multi-Item Intake System</p>
                </div>
            </div>
            <button onclick="closeQuotationModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-10">
            <form action="{{ route('cs.quotations.store', $lead->id) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                @csrf
                <div class="space-y-6">
                    <div class="p-6 bg-[#22AF85]/5 border-2 border-[#22AF85]/10 rounded-[2rem] flex items-center gap-5">
                        <div class="w-12 h-12 bg-[#22AF85] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <p class="text-gray-600 text-xs font-bold leading-relaxed">
                            <span class="text-[#22AF85] font-black uppercase tracking-widest block mb-1">Input Data Barang</span>
                            Anda bisa menambahkan beberapa barang sekaligus. Layanan akan dipilih saat Generate SPK.
                        </p>
                    </div>

                    {{-- Items Container --}}
                    <div class="space-y-8 mt-10">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-50/50 rounded-[2.5rem] p-8 border-2 border-gray-100 relative group transition-all duration-300 hover:border-[#22AF85]/20 hover:bg-white hover:shadow-2xl hover:shadow-gray-200/50">
                                {{-- Item Header --}}
                                <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-100">
                                    <div class="flex items-center gap-4">
                                        <span class="w-10 h-10 rounded-xl bg-[#22AF85] text-white flex items-center justify-center font-black text-sm shadow-lg shadow-green-100" x-text="index + 1"></span>
                                        <h4 class="text-xl font-black text-gray-900 uppercase tracking-tight">Data Item</h4>
                                    </div>
                                    <button type="button" @click="removeItem(index)" 
                                            x-show="items.length > 1"
                                            class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
                                        <span>Hapus Item</span>
                                    </button>
                                </div>

                                {{-- Item Data Form --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {{-- Category --}}
                                    <div class="space-y-3">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kategori Barang *</label>
                                        <div class="flex gap-3">
                                            <select x-model="item.categoryOpt" @change="item.category = item.categoryOpt === 'Lainnya' ? '' : item.categoryOpt" 
                                                    class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                <option value="">Pilih...</option>
                                                <option value="Sepatu">Sepatu</option>
                                                <option value="Tas">Tas</option>
                                                <option value="Dompet">Dompet</option>
                                                <option value="Topi">Topi</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][category]'" x-model="item.category" required
                                                   placeholder="Ketik manual..." 
                                                   class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                   :class="item.categoryOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                   :readonly="item.categoryOpt !== 'Lainnya'">
                                        </div>
                                    </div>

                                    {{-- Type --}}
                                    <div class="space-y-3">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jenis / Model</label>
                                        <div class="flex gap-3">
                                            <select x-model="item.typeOpt" @change="item.shoe_type = item.typeOpt === 'Lainnya' ? '' : item.typeOpt"
                                                    class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                <option value="">Pilih...</option>
                                                <option value="Casual">Casual</option>
                                                <option value="Sneakers">Sneakers</option>
                                                <option value="Outdoor">Outdoor</option>
                                                <option value="Sport">Sport</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][shoe_type]'" x-model="item.shoe_type"
                                                   placeholder="Ketik manual..." 
                                                   class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                   :class="item.typeOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                   :readonly="item.typeOpt !== 'Lainnya'">
                                        </div>
                                    </div>

                                    {{-- Brand --}}
                                    <div class="space-y-3">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Brand</label>
                                        <div class="flex gap-3">
                                            <select x-model="item.brandOpt" @change="item.shoe_brand = item.brandOpt === 'Lainnya' ? '' : item.brandOpt"
                                                    class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                <option value="">Pilih...</option>
                                                <option value="Nike">Nike</option>
                                                <option value="Adidas">Adidas</option>
                                                <option value="Puma">Puma</option>
                                                <option value="New Balance">New Balance</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][shoe_brand]'" x-model="item.shoe_brand"
                                                   placeholder="Ketik manual..." 
                                                   class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                   :class="item.brandOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                   :readonly="item.brandOpt !== 'Lainnya'">
                                        </div>
                                    </div>

                                    {{-- Size --}}
                                    <div class="space-y-3">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Ukuran</label>
                                        <div class="flex gap-3">
                                            <select x-model="item.sizeOpt" @change="item.shoe_size = item.sizeOpt === 'Lainnya' ? '' : item.sizeOpt"
                                                    class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                <option value="">Pilih...</option>
                                                <option value="40">40</option>
                                                <option value="41">41</option>
                                                <option value="42">42</option>
                                                <option value="43">43</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][shoe_size]'" x-model="item.shoe_size"
                                                   placeholder="Ketik manual..." 
                                                   class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                   :class="item.sizeOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                   :readonly="item.sizeOpt !== 'Lainnya'">
                                        </div>
                                    </div>

                                    {{-- Color & Notes --}}
                                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="space-y-3">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Warna</label>
                                            <input type="text" :name="'items[' + index + '][shoe_color]'" x-model="item.shoe_color"
                                                   placeholder="Contoh: Merah, Hitam Putih..." 
                                                   class="w-full px-6 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all">
                                        </div>
                                        <div class="space-y-3">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kondisi / Kerusakan</label>
                                            <textarea :name="'items[' + index + '][condition_notes]'" x-model="item.condition_notes" rows="1"
                                                      placeholder="Contoh: Kotor, sol lepas, pudar..." 
                                                      class="w-full px-6 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"></textarea>
                                        </div>
                                    </div>

                                    {{-- Item Notes (Keterangan Besar SPK) --}}
                                    <div class="col-span-1 md:col-span-2 mt-4">
                                        <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            Instruksi Khusus Produksi (Workshop)
                                        </label>
                                        <textarea :name="'items[' + index + '][item_notes]'" x-model="item.item_notes" rows="3"
                                                  placeholder="Catatan teknis pengerjaan untuk tim workshop..." 
                                                  class="w-full px-8 py-6 rounded-3xl border-2 border-[#22AF85]/10 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-[#22AF85]/5 placeholder-[#22AF85]/30 transition-all"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Add Item Button --}}
                    <button type="button" @click="addItem()" 
                            class="w-full py-8 border-4 border-dashed border-gray-100 rounded-[2.5rem] text-gray-400 hover:border-[#22AF85]/30 hover:text-[#22AF85] hover:bg-[#22AF85]/5 transition-all duration-300 group">
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-3xl group-scale-110 transition-transform">➕</span>
                            <span class="text-xs font-black uppercase tracking-[0.2em]">Tambah Item Lainnya</span>
                        </div>
                    </button>

                    {{-- Notes --}}
                    <div class="pt-10 border-t border-gray-100">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Catatan Umum Quotation</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-8 py-6 rounded-[2rem] border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-gray-50/30 transition-all" 
                                  placeholder="Tambahkan catatan syarat & ketentuan atau info umum..."></textarea>
                    </div>
                </div>

                <div class="flex gap-4 pt-10">
                    <button type="button" onclick="closeQuotationModal()" class="flex-1 px-8 py-6 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Batal</button>
                    <button type="submit" class="flex-[2] px-8 py-6 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Simpan & Terbitkan Quotation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function quotationManager() {
        return {
            items: [{
                categoryOpt: '',
                category: '',
                typeOpt: '',
                shoe_type: '',
                brandOpt: '',
                shoe_brand: '',
                sizeOpt: '',
                shoe_size: '',
                shoe_color: '',
                condition_notes: '',
                item_notes: ''
            }],
            addItem() {
                this.items.push({
                    categoryOpt: '',
                    category: '',
                    typeOpt: '',
                    shoe_type: '',
                    brandOpt: '',
                    shoe_brand: '',
                    sizeOpt: '',
                    shoe_size: '',
                    shoe_color: '',
                    condition_notes: '',
                    item_notes: ''
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            }
        }
    }
</script>
@endpush
