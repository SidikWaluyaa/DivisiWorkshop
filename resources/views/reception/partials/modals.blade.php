{{-- Edit Shoe Info Modal --}}
<div id="editShoeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Info Barang</h3>
            <form id="editShoeForm" onsubmit="updateShoeInfo(event)">
                <input type="hidden" id="editShoeOrderId" value="">
                
                <div class="mb-4">
                    <label for="editShoeBrand" class="block text-sm font-medium text-gray-700 mb-2">Brand Barang *</label>
                    <input type="text" id="editShoeBrand" name="shoe_brand" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                
                <div class="mb-4">
                    <label for="editShoeSize" class="block text-sm font-medium text-gray-700 mb-2">Ukuran *</label>
                    <input type="text" id="editShoeSize" name="shoe_size" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                
                <div class="mb-4">
                    <label for="editShoeColor" class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                    <input type="text" id="editShoeColor" name="shoe_color" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="mb-4">
                    <label for="editShoeCategory" class="block text-sm font-medium text-gray-700 mb-2">Jenis / Kategori</label>
                    <input type="text" id="editShoeCategory" name="category"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Contoh: Sneakers, Boots, dll">
                </div>
                
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeEditShoeModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Create Order Modal --}}
<div id="createOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-md bg-white my-10">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Tambah Order Manual</h3>
                <button onclick="closeCreateOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="createOrderForm" onsubmit="submitCreateOrder(event)" x-data="serviceSelector()">
                <div class="space-y-6">
                    {{-- Section 1: Data Customer --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-teal-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs">1</span>
                            Data Customer
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Customer *</label>
                                <input type="text" name="customer_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. WhatsApp *</label>
                                <input type="text" name="customer_phone" required placeholder="08..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Email (Opsional)</label>
                                <input type="email" name="customer_email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                            </div>
                            <div class="col-span-1 md:col-span-2 space-y-3">
                                <label class="block text-xs font-bold text-teal-800 uppercase tracking-wider">Alamat Lengkap & Pengiriman</label>
                                
                                {{-- Jalan / Detail --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">ALAMAT JALAN / DETAIL</label>
                                    <textarea name="customer_address" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm" placeholder="Nama Jalan, No. Rumah, RT/RW, Patokan..."></textarea>
                                </div>
                                
                                {{-- Grid for City/Prov --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Provinsi</label>
                                        <select id="manual_select_province" onchange="handleManualProvinceChange(this)" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                        <input type="hidden" name="customer_province" id="manual_input_province">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kota / Kabupaten</label>
                                        <select id="manual_select_city" onchange="handleManualCityChange(this)" disabled class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Kota --</option>
                                        </select>
                                        <input type="hidden" name="customer_city" id="manual_input_city">
                                    </div>
                                </div>
                                
                                {{-- Grid for District/Village/Zip --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kecamatan</label>
                                        <select id="manual_select_district" onchange="handleManualDistrictChange(this)" disabled class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                        <input type="hidden" name="customer_district" id="manual_input_district">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kelurahan</label>
                                        <select id="manual_select_village" onchange="handleManualVillageChange(this)" disabled class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                            <option value="">-- Pilih Kelurahan --</option>
                                        </select>
                                        <input type="hidden" name="customer_village" id="manual_input_village">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kode Pos</label>
                                    <input type="text" name="customer_postal_code" placeholder="Kode Pos" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                </div>
                                <p class="text-[10px] text-gray-500 italic mt-1">*Data alamat ini akan disimpan ke Master Customer untuk keperluan Ongkir di Finance nanti.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Data Order & SPK --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-teal-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs">2</span>
                            Data Order
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. SPK (Wajib Sesuai Nota Fisik) *</label>
                                <input type="text" name="spk_number" required placeholder="Contoh: SPK-2024-001"
                                    class="w-full px-3 py-2 border-2 border-teal-100 bg-white rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm font-mono font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Prioritas *</label>
                                <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="Reguler">Reguler</option>
                                    <option value="Prioritas">Prioritas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Masuk *</label>
                                <input type="date" name="entry_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Estimasi Selesai *</label>
                                <input type="date" name="estimation_date" required value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-teal-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs">3</span>
                            Identitas Barang
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Brand</label>
                                <input type="text" name="shoe_brand" required placeholder="Nike/Adidas..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Kategori</label>
                                <input type="text" name="category" placeholder="Sneakers/Boots..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Size</label>
                                <input type="text" name="shoe_size" required placeholder="42"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Color</label>
                                <input type="text" name="shoe_color" required placeholder="Red/Black..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Section 3B: Pilih Layanan (Dynamic Service Selection) --}}
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-indigo-800 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">3.B</span>
                                Pilih Layanan
                            </h4>
                            <button type="button" @click="showServiceModal = true" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-bold text-xs shadow-sm transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah
                            </button>
                        </div>
                        
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="selectedServices.length === 0">
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic text-xs">
                                                Belum ada layanan. Klik "Tambah" untuk memilih.
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(svc, index) in selectedServices" :key="index">
                                        <tr class="hover:bg-gray-50 group">
                                            <td class="px-3 py-2">
                                                <div class="font-bold text-xs text-gray-800" x-text="svc.name || svc.custom_name"></div>
                                                <div class="text-[10px] text-gray-500" x-show="svc.service_id === 'custom'">(Custom)</div>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    <template x-for="detail in svc.details">
                                                        <span class="bg-indigo-50 text-indigo-700 text-[9px] px-1.5 py-0.5 rounded border border-indigo-100" x-text="detail"></span>
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-right">
                                                <div class="text-xs font-bold text-gray-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(svc.price)"></div>
                                            </td>
                                            <td class="px-2 py-2 text-center w-8">
                                                <button type="button" @click="removeService(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                                
                                                <input type="hidden" :name="`services[${index}][service_id]`" :value="svc.service_id">
                                                <input type="hidden" :name="`services[${index}][custom_name]`" :value="svc.custom_name || svc.name">
                                                <input type="hidden" :name="`services[${index}][category]`" :value="svc.category">
                                                <input type="hidden" :name="`services[${index}][price]`" :value="svc.price">
                                                <input type="hidden" :name="`services[${index}][details]`" :value="JSON.stringify(svc.details)">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            
                            <div class="bg-gray-50 px-3 py-2 border-t border-gray-200 flex justify-between items-center" x-show="selectedServices.length > 0">
                                <span class="text-xs font-bold text-gray-600">Total</span>
                                <span class="text-sm font-black text-indigo-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateTotal())"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Service Modal (Inside x-data scope) --}}
                    <div x-show="showServiceModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;" x-cloak>
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showServiceModal = false">
                                <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
                            </div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </div>
                                        Tambah Layanan
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Kategori</label>
                                            <select x-model="serviceForm.category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="Custom">Custom / Manual</option>
                                                <template x-for="cat in uniqueCategories" :key="cat">
                                                    <option :value="cat" x-text="cat"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Layanan</label>
                                            <select x-model="serviceForm.service_id" @change="selectService()" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" :disabled="!serviceForm.category">
                                                <option value="">-- Pilih Layanan --</option>
                                                <template x-for="svc in filteredServices" :key="svc.id">
                                                    <option :value="svc.id" x-text="svc.name + ' (' + new Intl.NumberFormat('id-ID').format(svc.price) + ')'"></option>
                                                </template>
                                                <option value="custom">+ Input Manual (Custom)</option>
                                            </select>
                                        </div>

                                        <div x-show="serviceForm.service_id === 'custom'">
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Nama Layanan Custom</label>
                                            <input type="text" x-model="serviceForm.custom_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Repaint Khusus">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Harga (Rp)</label>
                                            <input type="number" x-model="serviceForm.price" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono font-bold">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Detail Tambahan (Opsional)</label>
                                            <div class="flex gap-2 mb-2">
                                                <input type="text" x-model="serviceForm.newDetail" @keydown.enter.prevent="addDetail()" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Jahit Sol, Extra Wangi">
                                                <button type="button" @click="addDetail()" class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 text-gray-700 font-bold border border-gray-300">+</button>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="(detail, idx) in serviceForm.details" :key="idx">
                                                    <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md text-xs border border-indigo-100 flex items-center gap-1 font-semibold">
                                                        <span x-text="detail"></span>
                                                        <button type="button" @click="removeDetail(idx)" class="text-indigo-400 hover:text-indigo-600 font-bold ml-1">&times;</button>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                    <button type="button" @click="saveService()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Simpan
                                    </button>
                                    <button type="button" @click="showServiceModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Catatan Order --}}
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-yellow-800 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-xs">4</span>
                            Catatan Order
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Keluhan / Request Customer (CS) *</label>
                                <textarea name="notes" rows="3" required placeholder="Jelaskan detail keluhan atau permintaan khusus pelanggan di sini..."
                                    class="w-full px-3 py-2 border-2 border-yellow-100 rounded-lg focus:ring-yellow-500 focus:border-yellow-500 text-sm italic"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Khusus Teknisi (Opsional)</label>
                                <textarea name="technician_notes" rows="2" placeholder="Pesan teknis untuk tim workshop (Misal: Hati-hati bahan suede...)"
                                    class="w-full px-3 py-2 border border-yellow-200 rounded-lg focus:ring-yellow-500 text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2 justify-end mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCreateOrderModal()" 
                            class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md font-bold text-sm">
                        Simpan & Lanjut Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Email Modal --}}
<div id="editEmailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Email Customer</h3>
            <form id="editEmailForm" onsubmit="updateEmail(event)">
                <input type="hidden" id="editOrderId" value="">
                <div class="mb-4">
                    <label for="editEmailInput" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="editEmailInput" name="email" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500" 
                            placeholder="customer@example.com">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika ingin menghapus email</p>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeEditEmailModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Data Order Modal --}}
<div id="editOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[70]">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-3">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Data Order
            </h3>
            <form id="editOrderForm" onsubmit="submitEditOrder(event)" class="space-y-4">
                <input type="hidden" id="edit_order_id">
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">No. SPK</label>
                    <input type="text" id="edit_spk_number" name="spk_number" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Nama Customer</label>
                        <input type="text" id="edit_customer_name" name="customer_name" required class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">No. WhatsApp</label>
                        <input type="text" id="edit_customer_phone" name="customer_phone" required class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Prioritas</label>
                    <select id="edit_priority" name="priority" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="Reguler">Reguler</option>
                        <option value="Prioritas">Prioritas</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Express">Express</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Keluhan Customer (CS)</label>
                    <textarea id="edit_notes" name="notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm italic bg-blue-50/30"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Instruksi Teknisi</label>
                    <textarea id="edit_technician_notes" name="technician_notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm bg-amber-50/30"></textarea>
                </div>

                <div class="flex gap-2 justify-end pt-4 border-t">
                    <button type="button" onclick="closeEditOrderModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 font-bold shadow-md transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div id="orderDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[60]">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-2xl rounded-xl bg-white my-10 animate-fade-in">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="detail_spk_number">Detail Order</h3>
                <div class="flex flex-col sm:flex-row sm:gap-4 mt-1">
                    <p class="text-xs text-gray-500" id="detail_entry_date"></p>
                    <p class="text-xs text-orange-600 font-bold" id="detail_estimation_date"></p>
                </div>
            </div>
            <button onclick="closeDetailModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-6">
            {{-- Customer Info --}}
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                <h4 class="text-xs font-bold text-indigo-700 uppercase mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Informasi Pelanggan
                </h4>
                <div class="grid grid-cols-2 gap-y-3 text-sm">
                    <div>
                        <span class="block text-[10px] text-indigo-400 font-bold uppercase">Nama</span>
                        <span class="font-bold text-gray-800" id="detail_customer_name">-</span>
                    </div>
                    <div>
                        <span class="block text-[10px] text-indigo-400 font-bold uppercase">No. WhatsApp</span>
                        <span class="font-bold text-gray-800" id="detail_customer_phone">-</span>
                    </div>
                    <div class="col-span-2">
                            <span class="block text-[10px] text-indigo-400 font-bold uppercase">Email</span>
                            <span class="font-bold text-gray-800" id="detail_customer_email">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-[10px] text-indigo-400 font-bold uppercase">Alamat</span>
                        <span class="text-gray-700 leading-relaxed" id="detail_customer_address">-</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Item Data --}}
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <h4 class="text-xs font-bold text-gray-600 uppercase mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Data Barang
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500">Brand</span>
                            <span class="font-bold text-gray-800" id="detail_shoe_brand">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500">Kategori</span>
                            <span class="font-bold text-gray-800" id="detail_category">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500">Warna</span>
                            <span class="font-bold text-gray-800" id="detail_shoe_color">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500">Ukuran</span>
                            <span class="font-bold text-gray-800" id="detail_shoe_size">-</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500">Prioritas</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase text-white" id="detail_priority">-</span>
                        </div>
                    </div>
                </div>

                {{-- Accessories Checklist --}}
                <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                    <h4 class="text-xs font-bold text-orange-700 uppercase mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Kelengkapan
                    </h4>
                    <div id="detail_accessories_list" class="grid grid-cols-2 gap-2 text-[10px]">
                        {{-- Populated by JS --}}
                    </div>
                </div>
            </div>
            
            {{-- Notes --}}
            <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                    <h4 class="text-xs font-bold text-yellow-700 uppercase mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Catatan Order
                </h4>
                <div class="space-y-3">
                    <div>
                        <span class="block text-[10px] text-yellow-600 font-bold uppercase">Keluhan / Request (CS)</span>
                        <p class="text-sm text-gray-800 bg-white p-2 rounded border border-yellow-200" id="detail_cs_notes">-</p>
                    </div>
                    <div>
                            <span class="block text-[10px] text-yellow-600 font-bold uppercase">Instruksi Teknisi</span>
                            <p class="text-sm text-gray-800 bg-white p-2 rounded border border-yellow-200" id="detail_technician_notes">-</p>
                    </div>
                </div>
            </div>

            {{-- Service Data --}}
            <div class="bg-white p-4 rounded-xl border border-gray-200">
                <h4 class="text-xs font-bold text-teal-700 uppercase mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Layanan Dipesan
                </h4>
                <div id="detail_services_list" class="space-y-2">
                    {{-- Populated by JS --}}
                </div>
                <div class="mt-4 pt-3 border-t flex justify-between items-center font-bold text-gray-900">
                    <span>Total Estimasi</span>
                    <span id="detail_total_price" class="text-teal-600">Rp 0</span>
                </div>
            </div>

            @if(isset($order->warehouse_qc_status))
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm">
                <span class="font-bold text-gray-700">Catatan QC:</span>
                <p class="text-gray-600 italic mt-1" id="detail_qc_notes">-</p>
            </div>
            @endif
        </div>

        <div class="mt-8 pt-4 border-t text-right">
            <button onclick="closeDetailModal()" class="px-6 py-2.5 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-all font-bold shadow-md">
                Tutup Detail
            </button>
        </div>
    </div>
</div>

{{-- Rack Storage Modal --}}
<div id='rackStorageModal' class='hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[80]'>
    <div class='relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white'>
        <div class='mt-3'>
             <h3 class='text-lg font-bold text-gray-900 mb-4 flex items-center gap-2'>
                <svg class='w-5 h-5 text-teal-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'></path></svg>
                Penyimpanan Rak Before
            </h3>
            <div class='bg-gray-50 p-3 rounded-lg border border-gray-100 mb-4'>
                <span class='text-xs text-gray-500 uppercase font-bold block'>No. SPK</span>
                <span id='storage_spk_number' class='text-lg font-mono font-bold text-gray-800'>SPK-XXX</span>
            </div>

            <div class='mb-4'>
                <label class='block text-sm font-medium text-gray-700 mb-2'>Pilih Rak Inbound (Transit) *</label>
                <select id='storage_rack_id' class='w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm'>
                    <option value=''>-- Pilih Rak --</option>
                    @if(isset($availableBeforeRacks))
                        @foreach($availableBeforeRacks as $rack)
                            <option value='{{ $rack->rack_code }}'>
                                {{ $rack->rack_code }} ({{ $rack->location }}) - Sisa: {{ $rack->capacity - $rack->current_count }}
                            </option>
                        @endforeach
                    @else
                        <option value='' disabled>Data rak tidak tersedia</option>
                    @endif
                </select>
                <p class='text-[10px] text-gray-500 mt-1 italic'>Rak kategori 'Before' untuk barang masuk.</p>
            </div>

            <div class='flex gap-2 justify-end'>
                <button type='button' onclick="document.getElementById('rackStorageModal').classList.add('hidden')" 
                        class='px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors'>
                    Batal
                </button>
                <button type='button' onclick='submitReceive()' 
                        class='px-4 py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg hover:from-teal-600 hover:to-teal-700 transition-all shadow-md font-bold'>
                    Simpan
                </button>
            </div>
            <input type='hidden' id='storage_order_id'>
        </div>
    </div>
</div>
