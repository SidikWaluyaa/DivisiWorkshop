<x-app-layout>
    <div class="h-screen bg-gray-50 flex flex-col items-center justify-center p-6">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-full">
            {{-- Header --}}
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white shrink-0">
                <h1 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Buat SPK Baru (Closing)
                </h1>
                <a href="{{ route('cs.dashboard') }}" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <form action="{{ route('cs.leads.store') }}" method="POST" id="spkForm" class="space-y-6" enctype="multipart/form-data" x-data="serviceSelector()">
                    @csrf
                    <input type="hidden" name="action" id="formAction" value="save">
                     {{-- Since we changed route to use storeSpk in previous step but called it store in route file?? No, I need to check routes.
                        Wait, I registered: Route::post('/leads', ... name('leads.store')) for NEW leads.
                        For SPK, I need a NEW route! I missed adding the route for storeSpk! 
                        I will fix the route in next step. For now I point to a placeholder name.
                     --}}
                     {{-- Correction: I haven't added the storeSpk route yet. I'll need to do that. --}}
                
                {{-- Temporary: Use a new route I will create --}}
                {{-- <form action="{{ route('cs.leads.spk.store', $lead->id) }}" ... --}}
                
                {{-- Let's assume I fix route first? No, parallel work. I will use the correct route name I INTEND to create: cs.leads.spk --}}
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- 1. Pengiriman & Kode --}}
                    <div class="space-y-4">
                        <h3 class="font-bold text-gray-800 border-b pb-2">1. Info Pengiriman</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Pengiriman</label>
                            <select name="delivery_type" id="delivery_type" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" required onchange="updateSpkPreview()">
                                <option value="">-- Pilih --</option>
                                <option value="N">N - Online</option>
                                <option value="P">P - Pickup</option>
                                <option value="J">J - Ojol</option>
                                <option value="F">F - Offline / Dateng</option>
                            </select>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kode CS (Manual)</label>
                            <input type="text" name="cs_code" id="cs_code_input" 
                                   value="{{ Auth::user()->cs_code ?? '' }}" 
                                   class="w-full border-gray-300 rounded-lg uppercase" 
                                   maxlength="3" 
                                   placeholder="Contoh: AD, QA"
                                   required
                                   oninput="updateSpkPreview()">
                        </div>
                        
                         <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                            <label class="block text-xs font-bold text-indigo-800 mb-1 uppercase">Preview Validasi SPK</label>
                            <div class="text-xl font-mono font-bold text-indigo-600 tracking-wider" id="spk_preview">
                                ?-{{ date('ym') }}-{{ date('d') }}-####-{{ Auth::user()->cs_code ?? 'XX' }}
                            </div>
                            <p class="text-[10px] text-indigo-500 mt-1">*Nomor urut (####) akan digenerate otomatis sistem.</p>
                        </div>
                    </div>

                    {{-- 2. Data Customer --}}
                    <div class="space-y-4">
                        <h3 class="font-bold text-gray-800 border-b pb-2">2. Data Customer</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Customer</label>
                            <input type="text" name="customer_name" value="{{ $lead->customer_name }}" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" placeholder="Nama Customer">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. WhatsApp</label>
                            <input type="text" name="customer_phone" value="{{ $lead->customer_phone }}" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Provinsi</label>
                                <select id="select_province" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm" onchange="handleProvinceChange(this)">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @if($lead->customer_province)
                                        <option value="{{ $lead->customer_province }}" selected>{{ $lead->customer_province }} (Selected)</option>
                                    @endif
                                </select>
                                <input type="hidden" name="customer_province" id="input_province" value="{{ $lead->customer_province }}">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Kota/Kabupaten</label>
                                <select id="select_city" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm" onchange="handleCityChange(this)" disabled>
                                    <option value="">-- Pilih Kota --</option>
                                    @if($lead->customer_city)
                                        <option value="{{ $lead->customer_city }}" selected>{{ $lead->customer_city }} (Selected)</option>
                                    @endif
                                </select>
                                <input type="hidden" name="customer_city" id="input_city" value="{{ $lead->customer_city }}">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                                <select id="select_district" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm" onchange="handleDistrictChange(this)" disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                <input type="hidden" name="customer_district" id="input_district">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan</label>
                                <select id="select_village" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 text-sm" onchange="handleVillageChange(this)" disabled>
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                                <input type="hidden" name="customer_village" id="input_village">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap (Untuk Delivery)</label>
                            <textarea name="customer_address" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" placeholder="Jalan, Blok, Nomor Rumah, RT/RW...">{{ $lead->customer_address }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 3. Detail Barang --}}
                <div class="mt-6 space-y-4">
                    <h3 class="font-bold text-gray-800 border-b pb-2">3. Detail Barang & Layanan</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Kategori</label>
                            <select name="category" class="w-full border-gray-300 rounded-lg" required>
                                <option value="Sepatu">Sepatu</option>
                                <option value="Tas">Tas</option>
                                <option value="Topi">Topi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Jenis</label>
                            <input type="text" name="shoe_type" class="w-full border-gray-300 rounded-lg" placeholder="Casual/Sneakers..." required>
                        </div>
                         <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Brand</label>
                            <input type="text" name="shoe_brand" class="w-full border-gray-300 rounded-lg" placeholder="Contoh: Nike" required>
                        </div>
                         <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Warna</label>
                            <input type="text" name="shoe_color" class="w-full border-gray-300 rounded-lg" placeholder="Hitam/Putih..." required>
                        </div>
                    </div>
                     <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Ukuran</label>
                            <input type="text" name="shoe_size" class="w-full border-gray-300 rounded-lg" placeholder="40/42..." required>
                        </div>
                         <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Prioritas</label>
                            <select name="priority" class="w-full border-gray-300 rounded-lg focus:ring-amber-500" required>
                                <option value="Reguler">Reguler (Normal)</option>
                                <option value="Prioritas">Prioritas (Cepat/Urgent)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Estimasi Selesai</label>
                            <input type="date" name="estimation_date" class="w-full border-gray-300 rounded-lg" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required>
                        </div>
                    </div>
                    
                    {{-- Dynamic Service Selection --}}
                    <div class="col-span-full space-y-4 border-t pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- LEFT: Services --}}
                            {{-- LEFT: Services (Dynamic Table) --}}
                            <div class="col-span-full md:col-span-1">
                                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="p-3 bg-indigo-50 border-b border-indigo-100 flex justify-between items-center">
                                        <h3 class="font-bold text-sm text-indigo-800">Pilih Layanan</h3>
                                        <button type="button" @click="showServiceModal = true" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-bold text-xs shadow-sm transition-colors flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Tambah
                                        </button>
                                    </div>
                                    
                                    <div class="p-0">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-if="selectedServices.length === 0">
                                                    <tr>
                                                        <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic text-xs">
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
                                                            
                                                            {{-- Hidden Inputs for Submission --}}
                                                            <input type="hidden" :name="`services[${index}][service_id]`" :value="svc.service_id">
                                                            <input type="hidden" :name="`services[${index}][custom_name]`" :value="svc.custom_name || svc.name">
                                                            <input type="hidden" :name="`services[${index}][category]`" :value="svc.category">
                                                            <input type="hidden" :name="`services[${index}][price]`" :value="svc.price">
                                                            <template x-for="detail in svc.details">
                                                                <input type="hidden" :name="`services[${index}][details][]`" :value="detail">
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                        
                                        {{-- Total --}}
                                        <div class="bg-gray-50 px-3 py-2 border-t border-gray-200 flex justify-between items-center" x-show="selectedServices.length > 0">
                                            <span class="text-xs font-bold text-gray-600">Total</span>
                                            <span class="text-sm font-black text-indigo-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateTotal())"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Service Modal --}}
                                <div x-show="showServiceModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
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
                                                    {{-- Category Select --}}
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

                                                    {{-- Service Select --}}
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

                                                    {{-- Custom Name --}}
                                                    <div x-show="serviceForm.service_id === 'custom'">
                                                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Nama Layanan Custom</label>
                                                        <input type="text" x-model="serviceForm.custom_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Repaint Khusus">
                                                    </div>

                                                    {{-- Price --}}
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Harga (Rp)</label>
                                                        <input type="number" x-model="serviceForm.price" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono font-bold">
                                                    </div>

                                                    {{-- Details --}}
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
                            </div>

                            {{-- RIGHT: Details & Photo --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Detail Jasa (Keterangan)</label>
                                    <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg mt-1 focus:ring-indigo-500" placeholder="Contoh: Ekstra wangi, bungkus plastik double..." >{{ $lead->notes }}</textarea>
                                </div>
                                
                                <div x-data="{ photoName: null, photoPreview: null }">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Foto Referensi (Opsional)</label>
                                    <input type="file" name="reference_photo" id="reference_photo" class="hidden" x-ref="photo"
                                            x-on:change="
                                                photoName = $refs.photo.files[0].name;
                                                const reader = new FileReader();
                                                reader.onload = (e) => { photoPreview = e.target.result; };
                                                reader.readAsDataURL($refs.photo.files[0]);
                                            " />

                                    <div class="mt-2" x-show="photoPreview" style="display: none;">
                                        <span class="block rounded-lg w-full h-40 bg-cover bg-center bg-no-repeat border border-gray-300"
                                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                        </span>
                                    </div>

                                    <button type="button" class="mt-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 underline" x-on:click.prevent="$refs.photo.click()">
                                        Upload Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </form>
            </div>

            {{-- Footer Actions --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center shrink-0">
                <button type="button" onclick="history.back()" class="text-gray-500 hover:text-gray-700 font-bold">Kembali</button>
                <div class="flex gap-3">
                    <button type="button" onclick="submitSpk('save_and_add')" class="px-4 py-2 bg-white text-indigo-600 border border-indigo-600 rounded-lg font-bold hover:bg-indigo-50 flex items-center gap-2 transform active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span>Simpan & Tambah Sepatu Lagi</span>
                    </button>
                    <button type="button" onclick="submitSpk('save')" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-lg flex items-center gap-2 transform active:scale-95 transition-all">
                        <span>Selesai & Generate SPK</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateSpkPreview() {
        const type = document.getElementById('delivery_type').value || '?';
        // Simple preview logic
        const date = '{{ date("ym") }}-{{ date("d") }}';
        const csInput = document.getElementById('cs_code_input').value.toUpperCase() || 'XX';
        document.getElementById('spk_preview').innerText = `${type}-${date}-####-${csInput}`;
    }

    function submitSpk(actionType) {
        // Set action value
        document.getElementById('formAction').value = actionType;
        
        const form = document.getElementById('spkForm');
        form.action = "{{ route('cs.leads.spk.store', $lead->id) }}"; 
        
        // Basic validation check (HTML5 validation triggers on submit(), but since we use button type=button, we must trigger it manually or change buttons to submit)
        // Let's use form.requestSubmit() if available, or manual check.
        if (form.checkValidity()) {
            form.submit();
        } else {
            form.reportValidity();
        }
    }

    // --- Regional Dropdown Logic (EMSifa API) ---
    const BASE_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    document.addEventListener('DOMContentLoaded', function() {
        fetchProvinces();
    });

    function fetchProvinces() {
        const select = document.getElementById('select_province');
        // Keep existing option if any
        // select.innerHTML = '<option value="">-- Pilih Provinsi --</option>'; 
        
        fetch(`${BASE_URL}/provinces.json`)
            .then(response => response.json())
            .then(data => {
                data.forEach(prov => {
                    const opt = document.createElement('option');
                    opt.value = prov.id;
                    opt.text = prov.name;
                    opt.dataset.name = prov.name;
                    select.appendChild(opt);
                });
            })
            .catch(err => console.error('Error fetching provinces:', err));
    }

    function handleProvinceChange(el) {
        // Ensure we get the name, handling the case where it might be a pre-filled value or a new API ID
        const selectedOption = el.options[el.selectedIndex];
        const provId = el.value;
        const provName = selectedOption.dataset.name || selectedOption.text.replace(' (Selected)', '');
        
        document.getElementById('input_province').value = provName;

        const citySelect = document.getElementById('select_city');
        const distSelect = document.getElementById('select_district');
        const villSelect = document.getElementById('select_village');

        // Reset Children
        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        citySelect.disabled = true;
        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        distSelect.disabled = true;
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;
        
        // Clear hidden inputs for children
        document.getElementById('input_city').value = '';
        document.getElementById('input_district').value = '';
        document.getElementById('input_village').value = '';

        if (provId && !isNaN(provId)) { // Only fetch if ID is numeric (from API)
            fetch(`${BASE_URL}/regencies/${provId}.json`)
                .then(response => response.json())
                .then(data => {
                    citySelect.disabled = false;
                    data.forEach(city => {
                        const opt = document.createElement('option');
                        opt.value = city.id;
                        opt.text = city.name;
                        opt.dataset.name = city.name;
                        citySelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching cities:', err));
        }
    }

    function handleCityChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const cityId = el.value;
        const cityName = selectedOption.dataset.name || selectedOption.text.replace(' (Selected)', '');
        
        document.getElementById('input_city').value = cityName;

        const distSelect = document.getElementById('select_district');
        const villSelect = document.getElementById('select_village');

        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        distSelect.disabled = true;
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;

        document.getElementById('input_district').value = '';
        document.getElementById('input_village').value = '';

        if (cityId && !isNaN(cityId)) {
            fetch(`${BASE_URL}/districts/${cityId}.json`)
                .then(response => response.json())
                .then(data => {
                    distSelect.disabled = false;
                    data.forEach(dist => {
                        const opt = document.createElement('option');
                        opt.value = dist.id;
                        opt.text = dist.name;
                        opt.dataset.name = dist.name;
                        distSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching districts:', err));
        }
    }

    function handleDistrictChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const distId = el.value;
        const distName = selectedOption.dataset.name || selectedOption.text;
        
        document.getElementById('input_district').value = distName;

        const villSelect = document.getElementById('select_village');
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;
        document.getElementById('input_village').value = '';

        if (distId && !isNaN(distId)) {
            fetch(`${BASE_URL}/villages/${distId}.json`)
                .then(response => response.json())
                .then(data => {
                    villSelect.disabled = false;
                    data.forEach(vill => {
                        const opt = document.createElement('option');
                        opt.value = vill.id;
                        opt.text = vill.name;
                        opt.dataset.name = vill.name;
                        villSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching villages:', err));
        }
    }

    function handleVillageChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const villName = selectedOption.dataset.name || selectedOption.text;
        document.getElementById('input_village').value = villName;
    }

    // Alpine Component for Service Selection
    function serviceSelector() {
        return {
            masterServices: @json($services),
            selectedServices: [],
            showServiceModal: false,
            serviceForm: {
                category: '',
                service_id: '',
                custom_name: '',
                price: 0,
                details: [],
                newDetail: ''
            },

            init() {
                // Initialize if needed
            },

            get uniqueCategories() {
                if (!Array.isArray(this.masterServices)) return [];
                return [...new Set(this.masterServices.map(s => s.category))].filter(Boolean);
            },
            
            get filteredServices() {
                if (!this.serviceForm.category) return [];
                return this.masterServices.filter(s => s.category === this.serviceForm.category);
            },

            selectService() {
                if (this.serviceForm.service_id === 'custom') {
                    this.serviceForm.custom_name = '';
                    this.serviceForm.price = 0;
                } else if (this.serviceForm.service_id) {
                    const svc = this.masterServices.find(s => s.id == this.serviceForm.service_id);
                    if (svc) {
                        this.serviceForm.custom_name = svc.name;
                        this.serviceForm.price = svc.price;
                    }
                }
            },

            addDetail() {
                if (this.serviceForm.newDetail.trim()) {
                    this.serviceForm.details.push(this.serviceForm.newDetail.trim());
                    this.serviceForm.newDetail = '';
                }
            },

            removeDetail(index) {
                this.serviceForm.details.splice(index, 1);
            },

            saveService() {
                // Validation
                if (!this.serviceForm.category || !this.serviceForm.service_id) {
                    alert('Harap pilih kategori dan layanan.');
                    return;
                }
                if (this.serviceForm.service_id === 'custom' && !this.serviceForm.custom_name) {
                    alert('Harap isi nama layanan custom.');
                    return;
                }

                // Add to list
                this.selectedServices.push({
                    service_id: this.serviceForm.service_id,
                    name: this.serviceForm.service_id === 'custom' ? this.serviceForm.custom_name : (this.masterServices.find(s => s.id == this.serviceForm.service_id)?.name || this.serviceForm.custom_name),
                    custom_name: this.serviceForm.custom_name,
                    category: this.serviceForm.category,
                    price: parseInt(this.serviceForm.price) || 0,
                    details: [...this.serviceForm.details] // Clone array
                });

                // Reset Form
                this.serviceForm = {
                    category: '',
                    service_id: '',
                    custom_name: '',
                    price: 0,
                    details: [],
                    newDetail: ''
                };
                this.showServiceModal = false;
            },

            removeService(index) {
                this.selectedServices.splice(index, 1);
            },

            calculateTotal() {
                return this.selectedServices.reduce((sum, svc) => sum + (parseInt(svc.price) || 0), 0);
            }
        }
    }
    </script>
</x-app-layout>
