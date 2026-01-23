<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-5xl mx-auto px-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Proses Penerimaan SPK
                    </h1>
                    <p class="text-gray-600 mt-1">Lengkapi data penerimaan barang dari customer</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="text-right">
                        <div class="text-xs text-gray-500">SPK Number</div>
                        <div class="text-2xl font-bold text-teal-600">{{ $order->spk_number }}</div>
                    </div>
                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white text-teal-700 border border-teal-200 rounded-lg hover:bg-teal-50 shadow-sm transition-all text-sm font-bold">
                        <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print SPK
                    </a>
                </div>
            </div>

            <form action="{{ route('reception.process-reception', $order->id) }}" method="POST" enctype="multipart/form-data" x-data="receptionForm()">
                @csrf

                {{-- Section 1: Data Customer --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        Data Customer & Waktu
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Customer</label>
                            <input type="text" name="customer_name" value="{{ $order->customer_name }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. WhatsApp</label>
                            <input type="text" name="customer_phone" value="{{ $order->customer_phone }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email (Opsional)</label>
                            <input type="email" name="customer_email" value="{{ $order->customer_email }}" placeholder="contoh@email.com" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                        </div>
                         <div class="col-span-1 md:col-span-1"></div> {{-- Spacer --}}

                        <div class="col-span-2 md:col-span-2 border-t pt-4 mt-2">
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Masuk (Entry Date)</label>
                                    <input type="datetime-local" name="entry_date" value="{{ $order->entry_date ? $order->entry_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Selesai (Due Date)</label>
                                    <input type="datetime-local" name="estimation_date" value="{{ $order->estimation_date ? $order->estimation_date->format('Y-m-d\TH:i') : '' }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika belum ditentukan.</p>
                                </div>
                             </div>
                        </div>

                        <div class="col-span-2 space-y-4">
                            <label class="block text-sm font-bold text-teal-800 uppercase tracking-wider">Alamat Lengkap & Pengiriman</label>
                            
                            {{-- Jalan / Detail --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">ALAMAT JALAN / DETAIL</label>
                                <textarea name="customer_address" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm" placeholder="Nama Jalan, No. Rumah, RT/RW, Patokan...">{{ $order->customer_address }}</textarea>
                            </div>
                            
                            {{-- Grid for City/Prov --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5Uppercase">Provinsi</label>
                                    <select id="select_province" onchange="handleProvinceChange(this)" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                        <option value="">{{ $order->customer->province ?? '-- Pilih Provinsi --' }}</option>
                                    </select>
                                    <input type="hidden" name="customer_province" id="input_province" value="{{ $order->customer->province ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5Uppercase">Kota / Kabupaten</label>
                                    <select id="select_city" onchange="handleCityChange(this)" {{ isset($order->customer->city) ? '' : 'disabled' }} class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                        <option value="">{{ $order->customer->city ?? '-- Pilih Kota --' }}</option>
                                    </select>
                                    <input type="hidden" name="customer_city" id="input_city" value="{{ $order->customer->city ?? '' }}">
                                </div>
                            </div>
                            
                            {{-- Grid for District/Village --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5Uppercase">Kecamatan</label>
                                    <select id="select_district" onchange="handleDistrictChange(this)" {{ isset($order->customer->district) ? '' : 'disabled' }} class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                        <option value="">{{ $order->customer->district ?? '-- Pilih Kecamatan --' }}</option>
                                    </select>
                                    <input type="hidden" name="customer_district" id="input_district" value="{{ $order->customer->district ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5Uppercase">Kelurahan</label>
                                    <select id="select_village" onchange="handleVillageChange(this)" {{ isset($order->customer->village) ? '' : 'disabled' }} class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                                        <option value="">{{ $order->customer->village ?? '-- Pilih Kelurahan --' }}</option>
                                    </select>
                                    <input type="hidden" name="customer_village" id="input_village" value="{{ $order->customer->village ?? '' }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5Uppercase">Kode Pos</label>
                                <input type="text" name="customer_postal_code" value="{{ $order->customer->postal_code ?? '' }}" placeholder="Kode Pos" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 text-sm">
                            </div>
                            <p class="text-xs text-gray-500 italic">*Data alamat ini tersinkronisasi dengan Master Customer untuk keperluan Ongkir di Finance.</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Data Sepatu (Basic Info) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        Data Sepatu
                    </h3>

                    <!-- Shoe Data (ReadOnly) -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Brand</label>
                            <input type="text" value="{{ $order->shoe_brand }}" readonly class="w-full border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis</label>
                            <input type="text" value="{{ $order->shoe_type ?? $order->category }}" readonly class="w-full border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Ukuran</label>
                            <input type="text" value="{{ $order->shoe_size }}" readonly class="w-full border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Warna</label>
                            <input type="text" value="{{ $order->shoe_color }}" readonly class="w-full border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-500 italic flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Input Layanan & Material akan dilakukan oleh Teknisi pada tahap Assessment & Sortir.
                    </div>
                </div>

                {{-- Section 3: Kelengkapan Aksesoris --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                        Kelengkapan Aksesoris
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                        {{-- Tali --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Tali <span class="text-red-500">*</span></label>
                            <div class="flex flex-col gap-2">
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-blue-50 focus-within:ring-2 focus-within:ring-blue-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_tali" value="Simpan" class="peer sr-only" required>
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Simpan</span>
                                </label>
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-orange-50 focus-within:ring-2 focus-within:ring-orange-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_tali" value="Nempel" class="peer sr-only">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-orange-600 peer-checked:border-orange-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Nempel</span>
                                </label>
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-gray-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_tali" value="Tidak Ada" class="peer sr-only">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-gray-600 peer-checked:border-gray-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Tidak Ada</span>
                                </label>
                            </div>
                        </div>

                        {{-- Insole --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Insole <span class="text-red-500">*</span></label>
                            <div class="flex flex-col gap-2">
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-blue-50 focus-within:ring-2 focus-within:ring-blue-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_insole" value="Simpan" class="peer sr-only" required>
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Simpan</span>
                                </label>
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-orange-50 focus-within:ring-2 focus-within:ring-orange-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_insole" value="Nempel" class="peer sr-only">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-orange-600 peer-checked:border-orange-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Nempel</span>
                                </label>
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-gray-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_insole" value="Tidak Ada" class="peer sr-only">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-gray-600 peer-checked:border-gray-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Tidak Ada</span>
                                </label>
                            </div>
                        </div>

                        {{-- Box --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Box <span class="text-red-500">*</span></label>
                            <div class="flex flex-col gap-2">
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-blue-50 focus-within:ring-2 focus-within:ring-blue-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_box" value="Simpan" class="peer sr-only" required>
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Simpan</span>
                                </label>
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-orange-50 focus-within:ring-2 focus-within:ring-orange-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_box" value="Nempel" class="peer sr-only">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-orange-600 peer-checked:border-orange-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Nempel</span>
                                </label>
                                <label class="relative flex items-center p-3 rounded-lg border cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-gray-500 transition-all border-gray-200">
                                    <input type="radio" name="accessories_box" value="Tidak Ada" class="peer sr-only">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-gray-600 peer-checked:border-gray-600 mr-2 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Tidak Ada</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Lainnya (Opsional)</label>
                        <input type="text" name="accessories_other" placeholder="Contoh: Kaos kaki, Pembersih, Tas, dll" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>

                {{-- Section 4: QC Gudang --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-sm font-bold">4</span>
                        QC Gudang
                    </h3>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Hasil QC <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-3 border-2 rounded-lg transition-all" :class="qcStatus === 'lolos' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-300'">
                                <input type="radio" name="warehouse_qc_status" value="lolos" x-model="qcStatus" required class="text-green-600 focus:ring-green-500">
                                <span class="font-semibold" :class="qcStatus === 'lolos' ? 'text-green-700' : 'text-gray-700'">✅ Lolos</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-3 border-2 rounded-lg transition-all" :class="qcStatus === 'tidak_lolos' ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-red-300'">
                                <input type="radio" name="warehouse_qc_status" value="tidak_lolos" x-model="qcStatus" required class="text-red-600 focus:ring-red-500">
                                <span class="font-semibold" :class="qcStatus === 'tidak_lolos' ? 'text-red-700' : 'text-gray-700'">❌ Tidak Lolos</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="qcStatus === 'tidak_lolos'" x-cloak x-transition class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <label class="block text-sm font-semibold text-red-700 mb-2">Keterangan Masalah <span class="text-red-500">*</span></label>
                        <textarea name="warehouse_qc_notes" rows="4" placeholder="Jelaskan masalah yang ditemukan secara detail...&#10;Contoh: Sol retak di bagian depan kanan, Upper sobek di sisi kiri bagian atas" class="w-full border-red-300 rounded-lg focus:ring-red-500 focus:border-red-500" :required="qcStatus === 'tidak_lolos'"></textarea>
                        <p class="text-xs text-red-600 mt-2">⚠️ Order akan otomatis dikirim ke CX untuk konfirmasi customer</p>
                    </div>
                </div>


                {{-- Section 5: Instruksi Khusus Teknisi --}}
                <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-yellow-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-yellow-100 text-yellow-700 rounded-full flex items-center justify-center text-sm font-bold">5</span>
                        Instruksi Khusus Teknisi
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan untuk Teknisi</label>
                        <textarea name="technician_notes" rows="3" placeholder="Pesan khusus untuk teknisi (Misal: Hati-hati bahan suede, jangan digosok keras...)" class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"></textarea>
                    </div>
                </div>

                {{-- Section 6: Upload Foto Before --}}
                <div class="bg-white rounded-xl shadow-sm border border-teal-200 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-sm font-bold">6</span>
                        Upload Foto Kondisi Awal (Before)
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Foto Sepatu (Opsional)
                            <span class="text-xs text-gray-500 font-normal ml-2">Maksimal 5 foto, masing-masing max 5MB</span>
                        </label>
                        <input 
                            type="file" 
                            name="photos[]" 
                            multiple 
                            accept="image/*"
                            class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500"
                            onchange="previewPhotos(event)"
                        >
                        <p class="text-xs text-gray-500 mt-2">
                            📸 Foto akan otomatis di-compress dan diberi watermark (logo + ukuran sepatu)
                        </p>
                        
                        <!-- Photo Preview Container -->
                        <div id="photoPreview" class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4"></div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between gap-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <a href="{{ route('reception.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition-colors">
                        ← Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg font-bold hover:from-teal-700 hover:to-teal-800 transition-all shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan & Proses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function receptionForm() {
        return {
            qcStatus: '',
            
            // Note: Service and Material input has been moved to Assessment & Sortir stages.
            // This view only handles Customer Data, Shoe Data, Accessories, Initial Photos, and QC Gudang.
        }
    }

    function previewPhotos(event) {
        const preview = document.getElementById('photoPreview');
        preview.innerHTML = '';
        
        const files = event.target.files;
        
        // Validation: Max 5 photos
        if (files.length > 5) {
            alert('Maksimal 5 foto!');
            event.target.value = '';
            return;
        }
        
        // Process each file
        Array.from(files).forEach((file, index) => {
            // Validation: Max 5MB per file
            if (file.size > 5 * 1024 * 1024) {
                alert(`File ${file.name} terlalu besar! Maksimal 5MB`);
                return;
            }
            
            // Validation: Image only
            if (!file.type.startsWith('image/')) {
                alert(`File ${file.name} bukan gambar!`);
                return;
            }
            
            // Create FileReader
            const reader = new FileReader();
            
            // Success handler
            reader.onload = function(e) {
                try {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border-2 border-teal-200" alt="Preview ${index + 1}">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs opacity-0 group-hover:opacity-100 font-bold">Foto ${index + 1}</span>
                        </div>
                    `;
                    preview.appendChild(div);
                } catch (error) {
                    console.error('Error creating preview:', error);
                }
            };
            
            // Error handler
            reader.onerror = function(error) {
                console.error('Error reading file:', error);
                alert(`Gagal membaca file ${file.name}`);
            };
            
            // Read file as Data URL
            try {
                reader.readAsDataURL(file);
            } catch (error) {
                console.error('Error starting file read:', error);
                alert(`Gagal memproses file ${file.name}`);
            }
        });
    // --- Regional Dropdown Logic (EMSifa API) ---
    const REGIONAL_API_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('select_province')) {
            fetchProvinces();
        }
    });

    function fetchProvinces() {
        const select = document.getElementById('select_province');
        const currentName = "{{ $order->customer->province ?? '' }}";
        
        fetch(`${REGIONAL_API_BASE}/provinces.json`)
            .then(response => response.json())
            .then(data => {
                data.forEach(prov => {
                    if (prov.name !== currentName) {
                        const opt = document.createElement('option');
                        opt.value = prov.id;
                        opt.text = prov.name;
                        opt.dataset.name = prov.name;
                        select.appendChild(opt);
                    } else {
                        // Update the current option with the ID if it matches
                        const firstOpt = select.options[0];
                        if (firstOpt && firstOpt.text === currentName) {
                            firstOpt.value = prov.id;
                            firstOpt.dataset.name = prov.name;
                        }
                    }
                });
            })
            .catch(err => console.error('Error fetching provinces:', err));
    }

    function handleProvinceChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const provId = el.value;
        const provName = selectedOption.dataset.name || selectedOption.text;
        document.getElementById('input_province').value = provName;

        const citySelect = document.getElementById('select_city');
        const distSelect = document.getElementById('select_district');
        const villSelect = document.getElementById('select_village');

        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        citySelect.disabled = true;
        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        distSelect.disabled = true;
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;

        document.getElementById('input_city').value = '';
        document.getElementById('input_district').value = '';
        document.getElementById('input_village').value = '';

        if (provId) {
            fetch(`${REGIONAL_API_BASE}/regencies/${provId}.json`)
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
        const cityName = selectedOption.dataset.name || selectedOption.text;
        document.getElementById('input_city').value = cityName;

        const distSelect = document.getElementById('select_district');
        const villSelect = document.getElementById('select_village');

        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        distSelect.disabled = true;
        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        villSelect.disabled = true;

        document.getElementById('input_district').value = '';
        document.getElementById('input_village').value = '';

        if (cityId) {
            fetch(`${REGIONAL_API_BASE}/districts/${cityId}.json`)
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

        if (distId) {
            fetch(`${REGIONAL_API_BASE}/villages/${distId}.json`)
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
