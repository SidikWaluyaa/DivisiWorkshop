{{-- Modal: Edit Detail Barang & Layanan (Governed Edit) --}}
<div id="editItemModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
        {{-- Modal Header --}}
        <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Detail Transaksi</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Sync to Master Production</p>
                </div>
            </div>
            <button onclick="closeEditItemModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-10">
            <div id="lockedItemWarning" class="hidden mb-10 p-6 bg-red-50 border-2 border-red-100 rounded-[2rem] flex items-start gap-5">
                <div class="w-12 h-12 flex-shrink-0 bg-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-red-900 font-black text-sm uppercase tracking-widest mb-1.5 text-red-500">Data Terkunci (Locked)</p>
                    <p class="text-gray-600 text-xs font-bold leading-relaxed">
                        Hanya Admin yang dapat merevisi data pada tahap ini karena SPK/Work Order sudah masuk ke antrean workshop.
                    </p>
                </div>
            </div>

            <form id="editItemForm" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Kategori Barang *</label>
                            <select name="category" id="item_category" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="">Pilih Kategori...</option>
                                <option value="Sepatu">Sepatu</option>
                                <option value="Tas">Tas</option>
                                <option value="Dompet">Dompet</option>
                                <option value="Topi">Topi</option>
                                <option value="Lainnya">Lainnya...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Model / Tipe</label>
                            <select name="shoe_type" id="item_type" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="">Pilih Tipe...</option>
                                <option value="Casual">Casual</option>
                                <option value="Sneakers">Sneakers</option>
                                <option value="Outdoor">Outdoor</option>
                                <option value="Sport">Sport</option>
                                <option value="Formal">Formal</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Merek (Brand)</label>
                            <select name="shoe_brand" id="item_brand" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="">Pilih Brand...</option>
                                <option value="Nike">Nike</option>
                                <option value="Adidas">Adidas</option>
                                <option value="New Balance">New Balance</option>
                                <option value="Converse">Converse</option>
                                <option value="Lainnya">Lainnya...</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Size</label>
                                <input type="text" name="shoe_size" id="item_size" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Warna</label>
                                <input type="text" name="shoe_color" id="item_color" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gray-50 rounded-[2rem] border-2 border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Master Layanan & Biaya</label>
                        <span class="px-3 py-1 bg-[#22AF85]/10 text-[#22AF85] rounded-lg text-[9px] font-black uppercase tracking-widest">Financial Sync Active</span>
                    </div>
                    
                    <div id="service_edit_checklist" class="max-h-[300px] overflow-y-auto mb-6 pr-2 space-y-3 custom-scrollbar">
                        @php $currentCategory = ''; @endphp
                        @foreach($services as $service)
                            @if($currentCategory !== $service->category)
                                <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest mt-6 mb-3 px-1 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#22AF85]"></span>
                                    {{ $service->category }}
                                </p>
                                @php $currentCategory = $service->category; @endphp
                            @endif
                            <div class="bg-white rounded-2xl border-2 border-white hover:border-[#22AF85]/20 p-4 transition duration-300 group shadow-sm">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" 
                                               class="service-edit-checkbox w-5 h-5 rounded-lg border-2 border-gray-100 text-[#22AF85] focus:ring-0"
                                               data-price="{{ $service->price }}" 
                                               onchange="toggleServiceDetail(this); calculateEditTotal()">
                                        <span class="text-xs font-bold text-gray-700 group-hover:text-gray-900 transition">{{ $service->name }}</span>
                                    </div>
                                    <span class="text-xs font-black text-[#22AF85]">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                </label>
                                <div id="detail_container_{{ $service->id }}" class="hidden mt-4 pt-4 border-t border-dashed border-gray-100">
                                    <input type="text" name="service_details[{{ $service->id }}]" id="service_detail_{{ $service->id }}"
                                           class="w-full px-5 py-3 text-[11px] border-2 border-gray-50 rounded-xl bg-gray-50 focus:bg-white focus:border-[#22AF85] focus:ring-0 transition"
                                           placeholder="Catatan pengerjaan khusus (misal: warna cat spesifik)...">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Custom Services --}}
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Layanan Tambahan (Custom)</span>
                            <button type="button" onclick="addCustomServiceRow()" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition shadow-lg shadow-gray-200">
                                + Tambah Layanan
                            </button>
                        </div>
                        <div id="edit_custom_services_container" class="space-y-3"></div>
                    </div>

                    <div class="flex items-center justify-between pt-8 border-t border-gray-200 mt-8">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Biaya Baru (IDR)</span>
                            <p class="text-xs text-gray-400 font-bold italic">Harga otomatis terakumulasi</p>
                        </div>
                        <div class="relative">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-sm font-black text-gray-400">Rp</span>
                            <input type="number" name="item_total_price" id="item_total_price" 
                                   class="w-48 pl-14 pr-6 py-4 rounded-2xl border-0 bg-white text-xl font-black text-gray-900 shadow-inner"
                                   readonly>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Catatan Khusus Workshop</label>
                    <textarea name="item_notes" id="item_notes" rows="3" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30" placeholder="Informasi teknis tambahan..."></textarea>
                </div>

                <div class="pt-8 border-t border-gray-100">
                    <label class="block text-[10px] font-black text-red-500 uppercase tracking-[0.2em] mb-3">Alasan Revisi & Perubahan *</label>
                    <textarea name="revision_reason" id="item_revision_reason" rows="3" required
                              class="w-full px-6 py-4 rounded-3xl border-2 border-red-100 focus:border-red-400 focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-red-50/10 placeholder-red-200"
                              placeholder="Mengapa data barang atau harga diubah?"></textarea>
                    <p class="mt-3 text-[9px] text-gray-400 font-bold uppercase tracking-widest text-center">Setiap revisi akan disinkronasikan ke SPK dan memicu log audit.</p>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditItemModal()" class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Batal</button>
                    <button type="submit" class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Update & Sync SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>
