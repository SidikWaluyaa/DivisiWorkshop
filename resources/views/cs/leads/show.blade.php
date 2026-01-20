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
                <form action="{{ route('cs.leads.store') }}" method="POST" id="spkForm" class="space-y-6" enctype="multipart/form-data">
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
                    </div>
                    
                    {{-- Dynamic Service Selection --}}
                    <div class="col-span-full space-y-4 border-t pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- LEFT: Services --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Jasa Utama & Sub Jasa</label>
                                @if($services->isEmpty())
                                    <div class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                                        Belum ada data master service.
                                        <input type="hidden" name="services[]" value="Jasa Custom">
                                    </div>
                                @else
                                    <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
                                        @foreach($services->groupBy('category') as $category => $items)
                                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                                <h4 class="font-bold text-sm text-indigo-700 mb-2 border-b border-indigo-100 pb-1 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                    {{ $category }} (Jasa Utama)
                                                </h4>
                                                <div class="space-y-1 ml-1">
                                                    @foreach($items as $svc)
                                                        <label class="flex items-start gap-2 cursor-pointer hover:bg-white p-1.5 rounded transition-colors border border-transparent hover:border-gray-200">
                                                            <input type="checkbox" name="services[]" value="{{ $svc->id }}" class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                            <div class="text-xs leading-snug">
                                                                <div class="font-semibold text-gray-800">{{ $svc->name }}</div>
                                                                <div class="text-gray-500 text-[10px]">{{ $svc->description }}</div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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
    </script>
</x-app-layout>
