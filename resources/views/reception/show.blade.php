<x-app-layout>
    <div class="min-h-screen bg-white py-8">
        <div class="max-w-5xl mx-auto px-6">
            {{-- Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 flex items-center gap-3 tracking-tight">
                        <div class="p-2 bg-[#22AF85]/10 rounded-xl">
                            <svg class="w-10 h-10 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        Penerimaan Gudang
                    </h1>
                    <p class="text-gray-500 mt-2 font-medium">Validasi fisik & kelengkapan barang sebelum proses
                        workshop</p>
                </div>
                <div class="flex flex-col items-end gap-3">
                    <div class="text-right">
                        <div class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em]">SPK NUMBER</div>
                        <div class="text-3xl font-black text-gray-900 leading-none">{{ $order->spk_number }}</div>
                    </div>
                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#FFC232] text-gray-900 rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 text-sm font-black shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        PRINT SPK
                    </a>
                </div>
            </div>

            <form action="{{ route('reception.process-reception', $order->id) }}" method="POST"
                enctype="multipart/form-data" x-data="receptionForm()">
                @csrf

                {{-- Section 1: Data Customer --}}
                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                        <span
                            class="w-10 h-10 bg-[#22AF85] text-white rounded-xl flex items-center justify-center text-lg font-black shadow-lg shadow-[#22AF85]/20">1</span>
                        DATA CUSTOMER & PENGIRIMAN
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama
                                    Customer</label>
                                <input type="text" name="customer_name" value="{{ $order->customer_name }}"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-bold text-gray-800 py-3 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No.
                                    WhatsApp</label>
                                <input type="text" name="customer_phone" value="{{ $order->customer_phone }}"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-bold text-gray-800 py-3 transition-all">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email
                                    (Opsional)</label>
                                <input type="email" name="customer_email" value="{{ $order->customer_email }}"
                                    placeholder="contoh@email.com"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-bold text-gray-800 py-3 transition-all">
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4">
                                <div>
                                    <label
                                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal
                                        Masuk</label>
                                    <input type="datetime-local" name="entry_date"
                                        value="{{ $order->entry_date ? $order->entry_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-bold text-gray-800 py-3 transition-all">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Estimasi
                                        Selesai</label>
                                    <input type="datetime-local" name="estimation_date"
                                        value="{{ $order->estimation_date ? $order->estimation_date->format('Y-m-d\TH:i') : '' }}"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-bold text-gray-800 py-3 transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
                            <label
                                class="block text-xs font-black text-[#22AF85] uppercase tracking-[0.2em] mb-4">DETAIL
                                ALAMAT PENGIRIMAN</label>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5">Alamat Jalan
                                    / Detail</label>
                                <textarea name="customer_address" rows="3"
                                    class="w-full bg-white border-gray-200 rounded-xl focus:ring-[#22AF85] text-sm font-bold text-gray-800"
                                    placeholder="Nama Jalan, No. Rumah, RT/RW, Patokan...">{{ $order->customer_address }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-1.5">Provinsi</label>
                                    <select id="select_province" onchange="handleProvinceChange(this)"
                                        class="w-full bg-white border-gray-200 rounded-xl focus:ring-[#22AF85] text-xs font-bold">
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                    <input type="hidden" name="customer_province" id="input_province"
                                        value="{{ $order->customer->province ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5">Kota /
                                        Kabupaten</label>
                                    <select id="select_city" onchange="handleCityChange(this)" {{ isset($order->customer->city) ? '' : 'disabled' }}
                                        class="w-full bg-white border-gray-200 rounded-xl focus:ring-[#22AF85] text-xs font-bold">
                                        <option value="">-- Pilih Kota --</option>
                                    </select>
                                    <input type="hidden" name="customer_city" id="input_city"
                                        value="{{ $order->customer->city ?? '' }}">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-1.5">Kecamatan</label>
                                    <select id="select_district" onchange="handleDistrictChange(this)" {{ isset($order->customer->district) ? '' : 'disabled' }}
                                        class="w-full bg-white border-gray-200 rounded-xl focus:ring-[#22AF85] text-xs font-bold">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                    <input type="hidden" name="customer_district" id="input_district"
                                        value="{{ $order->customer->district ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5">Kelurahan
                                        / Desa</label>
                                    <select id="select_village" onchange="handleVillageChange(this)" {{ isset($order->customer->village) ? '' : 'disabled' }}
                                        class="w-full bg-white border-gray-200 rounded-xl focus:ring-[#22AF85] text-xs font-bold">
                                        <option value="">-- Pilih Kelurahan --</option>
                                    </select>
                                    <input type="hidden" name="customer_village" id="input_village"
                                        value="{{ $order->customer->village ?? '' }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5">Kode
                                    Pos</label>
                                <input type="text" name="customer_postal_code"
                                    value="{{ $order->customer->postal_code ?? '' }}" placeholder="Kode Pos"
                                    class="w-full bg-white border-gray-200 rounded-xl focus:ring-[#22AF85] text-xs font-bold">
                            </div>
                            <p class="text-[10px] text-gray-400 italic mt-2 font-medium flex items-center gap-1">
                                <svg class="w-3 h-3 text-[#22AF85]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Data sinkron dengan Master Customer
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Data Barang (Basic Info) --}}
                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                        <span
                            class="w-10 h-10 bg-[#22AF85] text-white rounded-xl flex items-center justify-center text-lg font-black shadow-lg shadow-[#22AF85]/20">2</span>
                        DATA BARANG (FISIK)
                    </h3>

                    <div
                        class="flex flex-col md:flex-row md:items-center gap-6 p-6 bg-[#22AF85]/5 border border-[#22AF85]/10 rounded-2xl">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white rounded-xl shadow-sm border border-[#22AF85]/10">
                                @php
                                    $cat = strtolower($order->category ?? '');
                                @endphp
                                @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                    <svg class="w-8 h-8 text-[#22AF85]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                    <svg class="w-8 h-8 text-[#22AF85]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582">
                                        </path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8 text-[#22AF85]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-1">
                                    KATEGORI ITEM</div>
                                <template x-if="!isEditing && !isEmpty('{{ $order->category }}', 'Item')">
                                    <div class="text-2xl font-black text-gray-900 leading-none flex items-center gap-2">
                                        {{ strtoupper($order->category ?? 'Item') }}
                                        <button type="button" @click="isEditing = true"
                                            class="p-1 hover:bg-[#22AF85]/10 rounded transition-colors"
                                            title="Ubah Kategori">
                                            <svg class="w-4 h-4 text-gray-400 hover:text-[#22AF85]" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="isEditing || isEmpty('{{ $order->category }}', 'Item')">
                                    <select name="category"
                                        class="w-full bg-white border-gray-200 rounded-lg focus:ring-[#22AF85] text-sm font-black text-gray-800 py-1 shadow-sm">
                                        @foreach(['Sepatu', 'Tas', 'Topi', 'Dompet', 'Jaket', 'Helm', 'Lainnya'] as $catName)
                                            <option value="{{ $catName }}" {{ (strtolower($order->category ?? '') == strtolower($catName)) ? 'selected' : '' }}>{{ strtoupper($catName) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </template>

                            </div>
                        </div>
                        <div class="md:ml-auto">
                            <span
                                class="px-4 py-2 bg-[#22AF85] text-white text-[10px] font-black rounded-lg shadow-lg shadow-[#22AF85]/20 uppercase tracking-widest">DATA
                                TERVERIFIKASI CS</span>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Brand --}}
                        <div
                            class="p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-[#22AF85]/30 transition-all group overflow-hidden">
                            <label
                                class="flex items-center justify-between text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-[#22AF85] transition-colors">
                                <span>Brand</span>
                                <button type="button" @click="isEditing = true" class="hidden group-hover:block"
                                    x-show="!isEditing">
                                    <svg class="w-3 h-3 transition-colors hover:text-[#22AF85]" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </button>
                            </label>
                            <template x-if="!isEditing && !isEmpty('{{ $order->shoe_brand }}')">
                                <div class="text-lg font-black text-gray-900 truncate">{{ $order->shoe_brand ?? '-' }}
                                </div>
                            </template>
                            <template x-if="isEditing || isEmpty('{{ $order->shoe_brand }}')">
                                <input type="text" name="shoe_brand"
                                    :value="isEmpty('{{ $order->shoe_brand }}') ? '' : '{{ $order->shoe_brand }}'"
                                    placeholder="Input Brand..."
                                    class="w-full bg-white border-0 p-0 focus:ring-0 font-black text-gray-900 text-lg border-b-2 border-dashed border-gray-200 focus:border-[#22AF85] transition-colors">
                            </template>
                        </div>

                        {{-- Type --}}
                        <div
                            class="p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-[#22AF85]/30 transition-all group overflow-hidden">
                            <label
                                class="flex items-center justify-between text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-[#22AF85] transition-colors">
                                <span>Jenis / Model</span>
                                <button type="button" @click="isEditing = true" class="hidden group-hover:block"
                                    x-show="!isEditing">
                                    <svg class="w-3 h-3 transition-colors hover:text-[#22AF85]" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </button>
                            </label>
                            <template x-if="!isEditing && !isEmpty('{{ $order->shoe_type }}')">
                                <div class="text-lg font-black text-gray-900 truncate">{{ $order->shoe_type ?? '-' }}
                                </div>
                            </template>
                            <template x-if="isEditing || isEmpty('{{ $order->shoe_type }}')">
                                <input type="text" name="shoe_type"
                                    :value="isEmpty('{{ $order->shoe_type }}') ? '' : '{{ $order->shoe_type }}'"
                                    placeholder="Input Jenis..."
                                    class="w-full bg-white border-0 p-0 focus:ring-0 font-black text-gray-900 text-lg border-b-2 border-dashed border-gray-200 focus:border-[#22AF85] transition-colors">
                            </template>
                        </div>

                        {{-- Size --}}
                        <div
                            class="p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-[#22AF85]/30 transition-all group overflow-hidden">
                            <label
                                class="flex items-center justify-between text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-[#22AF85] transition-colors">
                                <span>Ukuran</span>
                                <button type="button" @click="isEditing = true" class="hidden group-hover:block"
                                    x-show="!isEditing">
                                    <svg class="w-3 h-3 transition-colors hover:text-[#22AF85]" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </button>
                            </label>
                            <template x-if="!isEditing && !isEmpty('{{ $order->shoe_size }}')">
                                <div class="text-lg font-black text-gray-900 truncate">{{ $order->shoe_size ?? '-' }}
                                </div>
                            </template>
                            <template x-if="isEditing || isEmpty('{{ $order->shoe_size }}')">
                                <input type="text" name="shoe_size"
                                    :value="isEmpty('{{ $order->shoe_size }}') ? '' : '{{ $order->shoe_size }}'"
                                    placeholder="UK/EUR..."
                                    class="w-full bg-white border-0 p-0 focus:ring-0 font-black text-gray-900 text-lg border-b-2 border-dashed border-gray-200 focus:border-[#22AF85] transition-colors">
                            </template>
                        </div>

                        {{-- Color --}}
                        <div
                            class="p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-[#22AF85]/30 transition-all group overflow-hidden">
                            <label
                                class="flex items-center justify-between text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-[#22AF85] transition-colors">
                                <span>Warna</span>
                                <button type="button" @click="isEditing = true" class="hidden group-hover:block"
                                    x-show="!isEditing">
                                    <svg class="w-3 h-3 transition-colors hover:text-[#22AF85]" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </button>
                            </label>
                            <template x-if="!isEditing && !isEmpty('{{ $order->shoe_color }}')">
                                <div class="text-lg font-black text-gray-900 truncate">{{ $order->shoe_color ?? '-' }}
                                </div>
                            </template>
                            <template x-if="isEditing || isEmpty('{{ $order->shoe_color }}')">
                                <input type="text" name="shoe_color"
                                    :value="isEmpty('{{ $order->shoe_color }}') ? '' : '{{ $order->shoe_color }}'"
                                    placeholder="Input Warna..."
                                    class="w-full bg-white border-0 p-0 focus:ring-0 font-black text-gray-900 text-lg border-b-2 border-dashed border-gray-200 focus:border-[#22AF85] transition-colors">
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Kelengkapan Aksesoris --}}
                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                        <span
                            class="w-10 h-10 bg-[#FFC232] text-gray-900 rounded-xl flex items-center justify-center text-lg font-black shadow-lg shadow-[#FFC232]/20">3</span>
                        KELENGKAPAN AKSESORIS
                    </h3>

                    @php
                        $tali = $order->accessories_tali ?? ($order->accessories_data['tali'] ?? null);
                        $insole = $order->accessories_insole ?? ($order->accessories_data['insole'] ?? null);
                        $box = $order->accessories_box ?? ($order->accessories_data['box'] ?? null);
                        $isPreFilled = !is_null($tali) && !is_null($insole) && !is_null($box);
                    @endphp

                    @if($isPreFilled)
                        <div class="bg-[#22AF85]/5 p-6 rounded-2xl border border-[#22AF85]/10">
                            <div
                                class="flex items-center gap-3 mb-6 text-[#22AF85] font-black text-sm uppercase tracking-widest">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Data Terinput (Manual Order)
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tali
                                        Sepatu</span>
                                    <div class="mt-1 font-black text-gray-900 flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-[#22AF85]"></div>
                                        {{ $tali }}
                                        <input type="hidden" name="accessories_tali" value="{{ $tali }}">
                                    </div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Insole</span>
                                    <div class="mt-1 font-black text-gray-900 flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-[#22AF85]"></div>
                                        {{ $insole }}
                                        <input type="hidden" name="accessories_insole" value="{{ $insole }}">
                                    </div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Box /
                                        Plastik</span>
                                    <div class="mt-1 font-black text-gray-900 flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-[#22AF85]"></div>
                                        {{ $box }}
                                        <input type="hidden" name="accessories_box" value="{{ $box }}">
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-6 pt-6 border-t border-[#22AF85]/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Lokasi
                                        Rak Aksesoris</span>
                                    @if(isset($currentAccessoryRack))
                                        <div
                                            class="inline-flex items-center gap-3 text-gray-900 font-black bg-[#FFC232] px-6 py-3 rounded-xl shadow-lg shadow-[#FFC232]/20">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            RAK: {{ $currentAccessoryRack }}
                                        </div>
                                        <input type="hidden" name="accessory_rack_code" value="{{ $currentAccessoryRack }}">
                                    @else
                                        <div class="text-sm text-gray-500 font-medium italic">Tidak ada kelengkapan yang
                                            disimpan di rak.</div>
                                    @endif
                                </div>

                                <div class="md:text-right">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Aksesoris
                                        Lainnya</span>
                                    <div class="text-sm font-bold text-gray-800">{{ $order->accessories_other ?: '-' }}
                                    </div>
                                    <input type="hidden" name="accessories_other" value="{{ $order->accessories_other }}">
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- EDITABLE VIEW --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                            {{-- Tali --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-black text-gray-900 uppercase tracking-widest">Tali <span
                                        class="text-[#FFC232]">*</span></label>
                                <div class="flex flex-col gap-3">
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85]/5">
                                        <input type="radio" name="accessories_tali" value="S" x-model="accTali"
                                            class="peer sr-only" required>
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span
                                            class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">SIMPAN</span>
                                    </label>
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_tali" value="N" x-model="accTali"
                                            class="peer sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span
                                            class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">NEMPEL</span>
                                    </label>
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_tali" value="T" x-model="accTali"
                                            class="peer sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">TIDAK
                                            ADA</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Insole --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-black text-gray-900 uppercase tracking-widest">Insole <span
                                        class="text-[#FFC232]">*</span></label>
                                <div class="flex flex-col gap-3">
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_insole" value="S" x-model="accInsole"
                                            class="peer sr-only" required>
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span
                                            class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">SIMPAN</span>
                                    </label>
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_insole" value="N" x-model="accInsole"
                                            class="peer sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span
                                            class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">NEMPEL</span>
                                    </label>
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_insole" value="T" x-model="accInsole"
                                            class="peer sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">TIDAK
                                            ADA</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Box --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-black text-gray-900 uppercase tracking-widest">Box <span
                                        class="text-[#FFC232]">*</span></label>
                                <div class="flex flex-col gap-3">
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_box" value="S" x-model="accBox"
                                            class="peer sr-only" required>
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span
                                            class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">SIMPAN</span>
                                    </label>
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_box" value="N" x-model="accBox"
                                            class="peer sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span
                                            class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">NEMPEL</span>
                                    </label>
                                    <label
                                        class="relative flex items-center p-4 rounded-xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="accessories_box" value="T" x-model="accBox"
                                            class="peer sr-only">
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85] mr-3 transition-all">
                                        </div>
                                        <span class="text-sm font-black text-gray-600 peer-checked:text-[#22AF85]">TIDAK
                                            ADA</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                            <div>
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Aksesoris
                                    Lainnya (Opsional)</label>
                                <input type="text" name="accessories_other"
                                    placeholder="Contoh: Kaos kaki, Pembersih, Tas, dll"
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-bold text-gray-800 py-3 transition-all">
                            </div>

                            <div class="bg-[#22AF85]/5 p-6 rounded-2xl border-2 border-dashed border-[#22AF85]/20">
                                <label class="block text-xs font-black text-[#22AF85] uppercase tracking-widest mb-3">Pilih
                                    Rak Penyimpanan <span x-show="showAccessoryRack" class="text-[#FFC232]">*</span></label>
                                <select name="accessory_rack_code"
                                    class="w-full bg-white border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-black text-gray-800"
                                    :required="showAccessoryRack">
                                    <option value="">-- PILIH LOKASI RAK --</option>
                                    @foreach($accessoryRacks as $rack)
                                        <option value="{{ $rack->rack_code }}">
                                            {{ $rack->rack_code }} - {{ $rack->location }} (Isi:
                                            {{ $rack->current_count }}/{{ $rack->capacity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Section 4: QC Gatekeeper --}}
                <div class="bg-gray-900 rounded-2xl p-8 mb-8 relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 p-8 opacity-10 text-[#FFC232]">
                        <svg class="w-48 h-48 rotate-12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-black text-white mb-8 flex items-center gap-3 relative z-10">
                        <span
                            class="w-10 h-10 bg-[#FFC232] text-gray-900 rounded-xl flex items-center justify-center text-lg font-black shadow-lg shadow-[#FFC232]/30">4</span>
                        QC GATEKEEPER (PEMERIKSAAN AWAL)
                    </h3>

                    <div class="relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <label class="cursor-pointer group">
                                <input type="radio" name="reception_qc_passed" value="1" x-model="qcPassed"
                                    class="peer sr-only">
                                <div
                                    class="text-center p-8 rounded-2xl border-2 border-gray-800 peer-checked:border-[#22AF85] peer-checked:bg-[#22AF85]/10 text-gray-500 peer-checked:text-[#22AF85] transition-all font-black shadow-lg group-hover:bg-gray-800/50 flex flex-col justify-center items-center gap-4">
                                    <div class="p-3 rounded-full bg-gray-800 peer-checked:bg-[#22AF85] transition-all">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="tracking-widest uppercase">LOLOS QC GUDANG</span>
                                </div>
                            </label>
                            <label class="cursor-pointer group">
                                <input type="radio" name="reception_qc_passed" value="0" x-model="qcPassed"
                                    class="peer sr-only">
                                <div
                                    class="text-center p-8 rounded-2xl border-2 border-gray-800 peer-checked:border-red-500 peer-checked:bg-red-500/10 text-gray-500 peer-checked:text-red-500 transition-all font-black shadow-lg group-hover:bg-gray-800/50 flex flex-col justify-center items-center gap-4">
                                    <div class="p-3 rounded-full bg-gray-800 peer-checked:bg-red-500 transition-all">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <span class="tracking-widest uppercase">REJECT / TOLAK</span>
                                </div>
                            </label>
                        </div>

                        {{-- Rejection Reason --}}
                        <div x-show="qcPassed == '0'" x-transition
                            class="mt-6 bg-red-500/10 p-6 rounded-2xl border border-red-500/20" style="display: none;">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-black text-red-400 mb-2 uppercase tracking-widest">Alasan
                                        Penolakan (Wajib)</label>
                                    <textarea name="reception_rejection_reason" rows="6"
                                        x-model="rejectionReason"
                                        @input="handleRejectionInput"
                                        placeholder="Jelaskan kondisi barang kenapa ditolak..."
                                        class="w-full px-4 py-3 bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-red-500 focus:border-red-500 font-bold text-sm font-mono"></textarea>
                                </div>

                                {{-- Suggested Services (Searchable & Custom) --}}
                                <div x-data="{
                                    search: '',
                                    selected: [],
                                    options: {{ $services->map(fn($s) => ['name' => $s->name, 'price' => $s->price])->toJson() }},
                                    isOpen: false,
                                    price: 0,
                                    formatPrice(price) {
                                        return new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(price);
                                    },
                                    get filteredOptions() {
                                        if (this.search === '') return this.options;
                                        return this.options.filter(option => 
                                            option.name.toLowerCase().includes(this.search.toLowerCase())
                                        );
                                    },
                                    add(item) {
                                        this.search = item.name;
                                        this.price = item.price;
                                        this.isOpen = false;
                                    },
                                    confirm() {
                                        if (this.search.trim() !== '') {
                                            const formatted = this.search.trim() + ' (' + this.formatPrice(this.price) + ')';
                                            if (!this.selected.includes(formatted)) {
                                                this.selected.push(formatted);
                                            }
                                            this.search = '';
                                            this.price = 0;
                                            this.isOpen = false;
                                        }
                                    },
                                    remove(index) {
                                        this.selected.splice(index, 1);
                                    }
                                }">
                                    <label class="block text-sm font-black text-blue-400 mb-2 uppercase tracking-widest">Layanan Yang Disarankan (Tambah Harga)</label>
                                    
                                    {{-- Hidden Inputs for Form Submission --}}
                                    <template x-for="item in selected">
                                        <input type="hidden" name="recommended_services[]" :value="item">
                                    </template>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        {{-- Search Input --}}
                                        <div class="relative md:col-span-2">
                                            <input type="text" x-model="search" 
                                                @focus="isOpen = true"
                                                @click.away="isOpen = false"
                                                @keydown.enter.prevent="confirm()"
                                                @keydown.escape="isOpen = false"
                                                class="w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-sm py-3 px-4"
                                                placeholder="Layanan yang disarankan...">
                                            
                                            {{-- Dropdown --}}
                                            <div x-show="isOpen && filteredOptions.length > 0" 
                                                class="absolute z-50 w-full mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                                <template x-for="option in filteredOptions">
                                                    <div @click="add(option)" 
                                                        class="px-4 py-2 cursor-pointer hover:bg-blue-500/20 text-gray-300 hover:text-white font-medium transition-colors flex justify-between items-center">
                                                        <span x-text="option.name"></span>
                                                        <span class="text-xs text-blue-400 font-bold" x-text="formatPrice(option.price)"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Manual Price Input --}}
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <input type="number" x-model="price" 
                                                    @keydown.enter.prevent="confirm()"
                                                    class="w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-sm py-3 pl-4"
                                                    placeholder="Harga">
                                            </div>
                                            <button type="button" @click="confirm()" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1.5 rounded-xl font-black text-xs transition-colors uppercase">
                                                TAMBAH
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Selected Tags --}}
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <template x-for="(item, index) in selected" :key="index">
                                            <div class="flex items-center gap-1 bg-blue-500/20 text-blue-400 px-3 py-1 rounded-lg border border-blue-500/20">
                                                <span x-text="item" class="text-xs font-black uppercase"></span>
                                                <button type="button" @click="remove(index)" class="hover:text-blue-300">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <p class="text-[10px] text-gray-400 mt-2 italic">
                                        * Layanan yang disarankan (wajib) dari hasil pengecekan gudang.
                                    </p>
                                </div>

                                {{-- Optional Services (Searchable & Custom) --}}
                                <div class="mt-8 border-t border-amber-500/10 pt-8" x-data="{
                                    search: '',
                                    selected: [],
                                    options: {{ $services->map(fn($s) => ['name' => $s->name, 'price' => $s->price])->toJson() }},
                                    isOpen: false,
                                    price: 0,
                                    formatPrice(price) {
                                        return new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(price);
                                    },
                                    get filteredOptions() {
                                        if (this.search === '') return this.options;
                                        return this.options.filter(option => 
                                            option.name.toLowerCase().includes(this.search.toLowerCase())
                                        );
                                    },
                                    add(item) {
                                        this.search = item.name;
                                        this.price = item.price;
                                        this.isOpen = false;
                                    },
                                    confirm() {
                                        if (this.search.trim() !== '') {
                                            const formatted = this.search.trim() + ' (' + this.formatPrice(this.price) + ')';
                                            if (!this.selected.includes(formatted)) {
                                                this.selected.push(formatted);
                                            }
                                            this.search = '';
                                            this.price = 0;
                                            this.isOpen = false;
                                        }
                                    },
                                    remove(index) {
                                        this.selected.splice(index, 1);
                                    }
                                }">
                                    <label class="block text-sm font-black text-amber-400 mb-2 uppercase tracking-widest">Saran Layanan (Opsional)</label>
                                    
                                    {{-- Hidden Inputs for Form Submission --}}
                                    <template x-for="item in selected">
                                        <input type="hidden" name="suggested_services[]" :value="item">
                                    </template>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        {{-- Search Input --}}
                                        <div class="relative md:col-span-2">
                                            <input type="text" x-model="search" 
                                                @focus="isOpen = true"
                                                @click.away="isOpen = false"
                                                @keydown.enter.prevent="confirm()"
                                                @keydown.escape="isOpen = false"
                                                class="w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-amber-500 focus:border-amber-500 font-bold text-sm py-3 px-4"
                                                placeholder="Saran layanan opsional...">
                                            
                                            {{-- Dropdown --}}
                                            <div x-show="isOpen && filteredOptions.length > 0" 
                                                class="absolute z-50 w-full mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                                <template x-for="option in filteredOptions">
                                                    <div @click="add(option)" 
                                                        class="px-4 py-2 cursor-pointer hover:bg-amber-500/20 text-gray-300 hover:text-white font-medium transition-colors flex justify-between items-center">
                                                        <span x-text="option.name"></span>
                                                        <span class="text-xs text-amber-400 font-bold" x-text="formatPrice(option.price)"></span>
                                                     </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Manual Price Input --}}
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <input type="number" x-model="price" 
                                                    @keydown.enter.prevent="confirm()"
                                                    class="w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-amber-500 focus:border-amber-500 font-bold text-sm py-3 pl-4"
                                                    placeholder="Harga">
                                            </div>
                                            <button type="button" @click="confirm()" 
                                                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-1.5 rounded-xl font-black text-xs transition-colors uppercase">
                                                TAMBAH
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Selected Tags --}}
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <template x-for="(item, index) in selected" :key="index">
                                            <div class="flex items-center gap-1 bg-amber-500/20 text-amber-400 px-3 py-1 rounded-lg border border-amber-500/20">
                                                <span x-text="item" class="text-xs font-black uppercase"></span>
                                                <button type="button" @click="remove(index)" class="hover:text-amber-300">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <p class="text-[10px] text-gray-400 mt-2 italic">
                                        * Saran layanan tambahan yang tidak wajib (Saran dari Workshop).
                                    </p>
                                </div>

                                {{-- Evidence Photos --}}
                                <div>
                                    <label class="block text-sm font-black text-red-400 mb-2 uppercase tracking-widest">Foto
                                        Bukti Kondisi (Opsional)</label>
                                    <input type="file" name="evidence_photos[]" multiple accept="image/*"
                                        class="block w-full text-sm text-gray-400
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-xl file:border-0
                                file:text-xs file:font-black
                                file:bg-gray-700 file:text-white
                                hover:file:bg-gray-600 transition-all
                                cursor-pointer">
                                    <p class="text-[10px] text-gray-400 mt-1 italic">* Bisa upload banyak foto sekaligus.
                                    </p>
                                </div>

                                <div
                                    class="flex items-center gap-2 pt-2 text-red-500 text-[10px] font-black uppercase tracking-widest border-t border-red-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    Order akan ditahan untuk konfirmasi CS
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 5: Rekap Layanan & Pesan CS --}}
                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                        <span
                            class="w-10 h-10 bg-[#22AF85] text-white rounded-xl flex items-center justify-center text-lg font-black shadow-lg shadow-[#22AF85]/20">5</span>
                        REKAP LAYANAN & PESAN CS
                    </h3>

                    {{-- 1. Layanan yang Disarankan --}}
                    <div class="mb-8">
                        <label class="block text-xs font-black text-[#22AF85] uppercase tracking-widest mb-4">Layanan
                            yang Disarankan CS:</label>
                        @if($order->workOrderServices->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($order->workOrderServices as $service)
                                    <div
                                        class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm hover:border-[#22AF85]/30 transition-all flex flex-col group relative overflow-hidden">
                                        {{-- Category Badge --}}
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-[9px] font-black bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md uppercase tracking-widest">
                                                {{ $service->category_name ?? $service->service->category ?? 'General' }}
                                            </span>
                                            <span class="text-[10px] font-black text-[#22AF85]">
                                                Rp {{ number_format($service->cost, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <span
                                            class="text-xs font-black text-gray-900 uppercase tracking-wider mb-1 group-hover:text-[#22AF85] transition-colors inline-block">
                                            {{ $service->custom_service_name ?? $service->service->name ?? 'Custom Service' }}
                                        </span>

                                        @if($service->service && $service->service->description)
                                            <p class="text-[10px] text-gray-500 font-medium mb-2 line-clamp-2">
                                                {{ $service->service->description }}
                                            </p>
                                        @endif

                                        @if(!empty($service->service_details) && is_array($service->service_details))
                                            <div class="mt-auto space-y-2">
                                                @if(isset($service->service_details['manual_detail']) && !empty($service->service_details['manual_detail']))
                                                    <div class="p-2 bg-yellow-50 border border-yellow-100 rounded-lg text-[10px] text-yellow-800 font-bold italic">
                                                        "{{ $service->service_details['manual_detail'] }}"
                                                    </div>
                                                @endif
                                                
                                                <div class="flex flex-wrap gap-1 pt-1">
                                                    @foreach($service->service_details as $key => $detail)
                                                        @if($key !== 'manual_detail' && !empty($detail))
                                                            <span
                                                                class="text-[8px] font-bold bg-[#22AF85]/5 text-[#22AF85] px-1.5 py-0.5 rounded-md border border-[#22AF85]/10">
                                                                #{{ is_array($detail) ? implode(', ', $detail) : $detail }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-sm text-gray-500 font-medium italic">
                                Belum ada layanan yang dipilih oleh CS.
                            </div>
                        @endif
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div class="p-6 bg-[#22AF85]/5 border-2 border-dashed border-[#22AF85]/20 rounded-2xl">
                                <label
                                    class="block text-xs font-black text-[#22AF85] uppercase tracking-widest mb-3">Pesan
                                    untuk Workshop (Dari CS)</label>
                                <div
                                    class="p-4 bg-white rounded-xl border border-[#22AF85]/10 text-gray-800 font-bold text-sm min-h-[80px]">
                                    "{{ $order->notes ?? $order->customer_notes ?? 'Tidak ada pesan khusus.' }}"
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Catatan
                                    Tambahan Gudang</label>
                                <textarea name="reception_notes" rows="3"
                                    placeholder="Tambahkan catatan jika ada kondisi khusus saat barang diterima..."
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] font-bold text-gray-800 py-3 transition-all">{{ $order->reception_notes }}</textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 h-full">
                            <div class="flex items-center gap-4 mb-6">
                                <div
                                    class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-200">
                                    <svg class="w-6 h-6 text-[#22AF85]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Customer
                                        Service</div>
                                    <div class="text-lg font-black text-gray-900">
                                        {{ $order->created_by_name ?? 'ADMIN' }}</div>
                                </div>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-500 uppercase">Input Tanggal</span>
                                    <span
                                        class="text-xs font-black text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-500 uppercase">Input Lokasi Store</span>
                                    <span
                                        class="text-xs font-black text-[#22AF85] uppercase tracking-wider">{{ $order->store->name ?? 'CENTRAL' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Section 6: Standardized Photo Upload & Gallery --}}
                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 mb-8" x-data="photoGallery()">
                    <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                        <span class="w-10 h-10 bg-[#22AF85] text-white rounded-xl flex items-center justify-center text-lg font-black shadow-lg shadow-[#22AF85]/20">6</span>
                        FOTO KONDISI AWAL (BEFORE)
                    </h3>

                    {{-- Photo Gallery Grid --}}
                    @if($order->photos->count() > 0)
                    <div class="mb-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Galeri Foto ({{ $order->photos->count() }})
                            </h4>
                            <div class="flex gap-2">
                                <button type="button" @click="isSelecting = !isSelecting" class="text-[10px] font-black uppercase px-3 py-1.5 rounded-lg transition-all" :class="isSelecting ? 'bg-gray-200 text-gray-600' : 'bg-[#22AF85] text-white shadow-md shadow-[#22AF85]/20'" x-text="isSelecting ? 'Batal' : 'Kelola'"></button>
                                <template x-if="isSelecting && selectedPhotos.length > 0">
                                    <button type="button" @click="deleteSelected()" class="text-[10px] font-black uppercase px-3 py-1.5 bg-red-500 text-white rounded-lg shadow-md shadow-red-200" x-text="'Hapus (' + selectedPhotos.length + ')'"></button>
                                </template>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach($order->photos as $photo)
                            <div class="relative aspect-square rounded-xl overflow-hidden border-2 transition-all duration-300 group" :class="selectedPhotos.includes({{ $photo->id }}) ? 'border-[#22AF85] ring-4 ring-[#22AF85]/10' : 'border-white shadow-sm hover:shadow-xl'">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 cursor-pointer" @click="handlePhotoClick('{{ asset('storage/' . $photo->file_path) }}', {{ $photo->id }})">
                                
                                {{-- Selection Overlay --}}
                                <div x-show="isSelecting" class="absolute inset-0 bg-black/20 flex items-start justify-end p-2 pointer-events-none">
                                    <div class="w-5 h-5 rounded-full border-2 border-white shadow-sm flex items-center justify-center transition-colors" :class="selectedPhotos.includes({{ $photo->id }}) ? 'bg-[#22AF85]' : 'bg-white/50'">
                                        <svg x-show="selectedPhotos.includes({{ $photo->id }})" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>

                                {{-- Quick Actions (Visible on Hover) --}}
                                <div x-show="!isSelecting" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <button type="button" @click="setAsCover({{ $photo->id }})" class="p-2 bg-[#FFC232] text-gray-900 rounded-lg hover:scale-110 transition-transform shadow-lg" title="Set sebagai Cover">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                    </button>
                                    <button type="button" @click="window.open('{{ asset('storage/' . $photo->file_path) }}', '_blank')" class="p-2 bg-white text-gray-900 rounded-lg hover:scale-110 transition-transform shadow-lg" title="Lihat Fullscreen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>

                                {{-- Labels --}}
                                <div class="absolute bottom-2 left-2 flex flex-col gap-1 pointer-events-none">
                                    <span class="px-2 py-0.5 bg-black/60 text-white text-[8px] font-black uppercase rounded-full backdrop-blur-md">{{ $photo->step }}</span>
                                    @if($photo->is_spk_cover)
                                    <span class="px-2 py-0.5 bg-[#FFC232] text-gray-900 text-[8px] font-black uppercase rounded-full shadow-sm">COVER</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Standard Upload Button --}}
                    <div class="flex justify-center">
                        <div class="relative">
                            <button type="button" id="upload-btn" class="px-10 py-4 bg-[#22AF85] text-white font-black rounded-2xl hover:bg-[#1b8e6b] hover:shadow-xl hover:-translate-y-1 transition-all flex items-center gap-3 uppercase tracking-widest text-sm shadow-md group">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                PILIH FOTO & UPLOAD
                            </button>
                            <input type="file" id="file_input" class="hidden" multiple accept="image/*">
                            
                            {{-- Progress Overlay (Overlaying the button area) --}}
                            <div id="upload-loading" class="hidden absolute inset-0 bg-white/95 backdrop-blur-sm rounded-2xl flex-col items-center justify-center z-10 p-2 border border-[#22AF85]/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 border-4 border-[#22AF85]/20 border-t-[#22AF85] rounded-full animate-spin"></div>
                                    <span class="text-xs font-black text-gray-900" id="upload-progress-text">0%</span>
                                    <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-wider" id="upload-status-label">Uploading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div
                    class="flex flex-col-reverse md:flex-row items-center justify-between gap-6 pt-10 border-t border-gray-100 mb-20">
                    <a href="{{ route('reception.index') }}"
                        class="w-full md:w-auto px-10 py-4 bg-gray-100 text-gray-500 font-black rounded-2xl hover:bg-gray-200 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-sm shadow-sm group">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        BATAL
                    </a>

                    <button type="submit"
                        class="w-full md:w-auto px-16 py-5 bg-[#FFC232] text-gray-900 font-black rounded-2xl hover:shadow-2xl hover:shadow-[#FFC232]/20 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 uppercase tracking-[0.2em] text-lg shadow-xl border-b-4 border-black/10 active:border-0 active:translate-y-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        SIMPAN & PROSES
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Accessory Rack Logic (Vanilla JS) ---
            // Mimics the logic from Manual Order (index.blade.php)
            const accContainer = document.getElementById('accessory_rack_container'); // Ensure ID exists in view
            const accSelect = document.querySelector('select[name="accessory_rack_code"]');

            // Select all radio buttons for accessories
            const accInputs = document.querySelectorAll('input[type="radio"][name^="accessories_"]');

            function checkAccessoryStorage() {
                let hasStored = false;

                // Check Tali, Insole, Box
                const checkList = ['accessories_tali', 'accessories_insole', 'accessories_box'];

                checkList.forEach(name => {
                    const checkedInput = document.querySelector(`input[name="${name}"]:checked`);
                    if (checkedInput && (checkedInput.value === 'S' || checkedInput.value === 'Simpan')) {
                        hasStored = true;
                    }
                });

                if (hasStored) {
                    // Show Dropdown
                    if (accContainer) {
                        accContainer.classList.remove('hidden');
                        // Add animation classes manually if needed, or rely on CSS transitions
                        accContainer.style.display = 'block';
                    }
                    if (accSelect) accSelect.required = true;
                } else {
                    // Hide Dropdown
                    if (accContainer) {
                        accContainer.classList.add('hidden');
                        accContainer.style.display = 'none';
                    }
                    if (accSelect) {
                        accSelect.required = false;
                        accSelect.value = '';
                    }
                }
            }

            // Add event listeners
            accInputs.forEach(input => {
                input.addEventListener('change', checkAccessoryStorage);
            });

            // Run once on load to handle pre-filled data (validation errors etc)
            checkAccessoryStorage();

            // --- QC Logic (Alpine Replacement / Hybrid) ---
            // We can keep Alpine for QC or move to Vanilla. 
            // For minimal disruption, we'll leave Alpine for QC but access it if needed.
        });

        // Photo Preview Logic


        // Alpine Data
        function receptionForm() {
            return {
                // Accessories State
                accTali: '{{ $tali ?? "T" }}',
                accInsole: '{{ $insole ?? "T" }}',
                accBox: '{{ $box ?? "T" }}',

                // QC State
                qcPassed: '1',
                rejectionReason: "1. Upper           : \n2. Sol             : \n3. Kondisi Bawaan  : ",

                handleRejectionInput(e) {
                    const prefixes = [
                        "1. Upper           : ",
                        "2. Sol             : ",
                        "3. Kondisi Bawaan  : "
                    ];
                    let lines = this.rejectionReason.split('\n');

                    // If more than 3 lines, we might want to prevent or handle it
                    // But for now, let's just ensure the first 3 lines have the correct prefixes
                    let modified = false;
                    prefixes.forEach((prefix, i) => {
                        if (!lines[i] || !lines[i].startsWith(prefix)) {
                            // Restore prefix if missing or tampered
                            const content = lines[i] ? lines[i].replace(/^\d+\.\s*(Upper|Sol|Kondisi Bawaan)\s*:\s*/i, '') : '';
                            lines[i] = prefix + (content ? content.trim() : '');
                            modified = true;
                        }
                    });

                    // Ensure we don't accidentally lose the 3rd line if it was empty
                    if (lines.length < 3) {
                        for (let i = lines.length; i < 3; i++) {
                            lines.push(prefixes[i]);
                        }
                        modified = true;
                    }

                    if (modified) {
                        this.rejectionReason = lines.join('\n');
                    }
                },

                // Editing State
                isEditing: {{ (
    in_array(strtolower($order->shoe_brand ?? ''), ['', 'unknown', '-', 'item']) ||
    in_array(strtolower($order->shoe_size ?? ''), ['', 'unknown', '-', 'item']) ||
    in_array(strtolower($order->category ?? 'item'), ['', 'unknown', '-', 'item'])
) ? 'true' : 'false' }},

                isEmpty(val, placeholder = 'Unknown') {
                    if (!val) return true;
                    const v = val.trim().toLowerCase();
                    return v === '' || v === '-' || v === 'unknown' || v === placeholder.toLowerCase();
                },

                get showAccessoryRack() {
                    return (this.accTali === 'S' || this.accTali === 'Simpan') ||
                        (this.accInsole === 'S' || this.accInsole === 'Simpan') ||
                        (this.accBox === 'S' || this.accBox === 'Simpan');
                }
            };
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function photoGallery() {
            return {
                isLightboxOpen: false, 
                currentImage: '', 
                currentPhotoId: null,
                isSelecting: false,
                selectedPhotos: [],
                allPhotoIds: [@foreach($order->photos as $p){{ $p->id }}{{ !$loop->last ? ',' : '' }}@endforeach],

                handlePhotoClick(imageUrl, photoId) {
                    if (this.isSelecting) {
                        this.toggleSelection(photoId);
                    } else {
                        window.open(imageUrl, '_blank');
                    }
                },
                toggleSelection(id) {
                    if (this.selectedPhotos.includes(id)) {
                        this.selectedPhotos = this.selectedPhotos.filter(i => i !== id);
                    } else {
                        this.selectedPhotos.push(id);
                    }
                },
                deleteSelected() {
                    if(!confirm(`Hapus ${this.selectedPhotos.length} foto secara PERMANEN?`)) return;
                    fetch('{{ route("photos.bulk-destroy") }}', { 
                        method: 'DELETE', 
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ ids: this.selectedPhotos })
                    })
                    .then(response => response.json())
                    .then(data => { 
                        if (data.success) { 
                            location.reload(); 
                        } else {
                            alert('Gagal menghapus: ' + data.message);
                        }
                    });
                },
                setAsCover(id) {
                    if(!confirm('Set foto ini sebagai Cover SPK?')) return;
                    
                    fetch(`/photos/${id}/set-cover`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Gagal mengatur cover: ' + data.message);
                        }
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const uploadBtn = document.getElementById('upload-btn');
            const fileInput = document.getElementById('file_input');
            const loadingOverlay = document.getElementById('upload-loading');
            const progressText = document.getElementById('upload-progress-text');
            const progressCircle = document.getElementById('upload-progress-circle');
            const statusLabel = document.getElementById('upload-status-label');
            
            let uploadedPhotoIds = [];

            if (!uploadBtn) return;

            const resumable = new Resumable({
                target: '{{ route("work-order-photos.chunk", $order->id) }}',
                query: { 
                    _token: '{{ csrf_token() }}',
                    step: 'WAREHOUSE_BEFORE'
                },
                fileType: ['jpg', 'jpeg', 'png', 'webp'],
                chunkSize: 1 * 1024 * 1024, // 1MB
                simultaneousUploads: 1,
                testChunks: false
            });

            resumable.assignBrowse(fileInput);
            resumable.assignBrowse(uploadBtn);

            resumable.on('filesAdded', function(files) {
                loadingOverlay.classList.remove('hidden');
                loadingOverlay.classList.add('flex');
                uploadedPhotoIds = [];
                resumable.upload();
            });

            resumable.on('fileProgress', function(file) {
                const progress = Math.floor(resumable.progress() * 100);
                progressText.innerText = progress + '%';
                if (progressCircle) {
                    const offset = 377 - (377 * progress / 100);
                    progressCircle.style.strokeDashoffset = offset;
                }
            });

            resumable.on('fileSuccess', function(file, message) {
                try {
                    const response = JSON.parse(message);
                    if (response.photo_id) {
                        uploadedPhotoIds.push(response.photo_id);
                    }
                } catch (e) {}
            });

            resumable.on('complete', function() {
                statusLabel.innerText = 'FINISHING...';
                setTimeout(() => {
                    processSequential(uploadedPhotoIds);
                }, 1000);
            });

            resumable.on('error', function(message, file) {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Gagal',
                    text: message || 'Terjadi kesalahan saat mengupload file.'
                });
                loadingOverlay.classList.add('hidden');
            });

            async function processSequential(ids) {
                if (ids.length === 0) {
                    location.reload();
                    return;
                }

                statusLabel.innerText = 'COMPRESSING...';
                for (let i = 0; i < ids.length; i++) {
                    const pid = ids[i];
                    progressText.innerText = `${i + 1}/${ids.length}`;
                    try {
                        await fetch(`/photos/${pid}/process`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                    } catch (err) {}
                }
                statusLabel.innerText = 'DONE!';
                setTimeout(() => { location.reload(); }, 500);
            }
        });

        // --- Regional Dropdown Logic (Laravel Proxy) ---
    const REGIONAL_API_BASE = '/regional';

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Address Init: DOM Loaded');
        if (document.getElementById('select_province')) {
            initAddressSections();
        }
    });

    async function initAddressSections() {
        const provSelect = document.getElementById('select_province');
        const citySelect = document.getElementById('select_city');
        const distSelect = document.getElementById('select_district');
        const villSelect = document.getElementById('select_village');

        const currentProvName = "{{ $order->customer->province ?? '' }}";
        const currentCityName = "{{ $order->customer->city ?? '' }}";
        const currentDistName = "{{ $order->customer->district ?? '' }}";
        const currentVillName = "{{ $order->customer->village ?? '' }}";

        console.log('Current Data:', { currentProvName, currentCityName, currentDistName, currentVillName });

        try {
            // 1. Fetch Provinces
            console.log('Fetching Provinces...');
            const provResponse = await fetch(`${REGIONAL_API_BASE}/provinces`);
            if (!provResponse.ok) throw new Error('Failed to fetch provinces');
            const provinces = await provResponse.json();
            console.log(`Fetched ${provinces.length} provinces`);

            if (provSelect) {
                provSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                
                let foundProvId = null;
                provinces.forEach(prov => {
                    const opt = document.createElement('option');
                    opt.value = prov.id;
                    opt.text = prov.name;
                    opt.dataset.name = prov.name;
                    if (currentProvName && prov.name.toLowerCase() === currentProvName.toLowerCase()) {
                        opt.selected = true;
                        foundProvId = prov.id;
                        const inputProv = document.getElementById('input_province');
                        if (inputProv) inputProv.value = prov.name;
                    }
                    provSelect.appendChild(opt);
                });

                if (foundProvId) {
                    console.log(`Matching province found: ${currentProvName} (ID: ${foundProvId})`);
                    // 2. Fetch Cities
                    if (citySelect) {
                        citySelect.disabled = false;
                        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                        console.log(`Fetching cities for province ${foundProvId}...`);
                        const cityResponse = await fetch(`${REGIONAL_API_BASE}/regencies/${foundProvId}`);
                        const cities = await cityResponse.json();
                        
                        let foundCityId = null;
                        cities.forEach(city => {
                            const opt = document.createElement('option');
                            opt.value = city.id;
                            opt.text = city.name;
                            opt.dataset.name = city.name;
                            if (currentCityName && city.name.toLowerCase() === currentCityName.toLowerCase()) {
                                opt.selected = true;
                                foundCityId = city.id;
                                const inputCity = document.getElementById('input_city');
                                if (inputCity) inputCity.value = city.name;
                            }
                            citySelect.appendChild(opt);
                        });

                        if (foundCityId) {
                            console.log(`Matching city found: ${currentCityName} (ID: ${foundCityId})`);
                            // 3. Fetch Districts
                            if (distSelect) {
                                distSelect.disabled = false;
                                distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                                const distResponse = await fetch(`${REGIONAL_API_BASE}/districts/${foundCityId}`);
                                const districts = await distResponse.json();
                                
                                let foundDistId = null;
                                districts.forEach(dist => {
                                    const opt = document.createElement('option');
                                    opt.value = dist.id;
                                    opt.text = dist.name;
                                    opt.dataset.name = dist.name;
                                    if (currentDistName && dist.name.toLowerCase() === currentDistName.toLowerCase()) {
                                        opt.selected = true;
                                        foundDistId = dist.id;
                                        const inputDist = document.getElementById('input_district');
                                        if (inputDist) inputDist.value = dist.name;
                                    }
                                    distSelect.appendChild(opt);
                                });
                                
                                if (foundDistId) {
                                    console.log(`Matching district found: ${currentDistName} (ID: ${foundDistId})`);
                                    // 4. Fetch Villages
                                    if (villSelect) {
                                        villSelect.disabled = false;
                                        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                                        const villResponse = await fetch(`${REGIONAL_API_BASE}/villages/${foundDistId}`);
                                        const villages = await villResponse.json();
                                        
                                        villages.forEach(vill => {
                                            const opt = document.createElement('option');
                                            opt.value = vill.id;
                                            opt.text = vill.name;
                                            opt.dataset.name = vill.name;
                                            if (currentVillName && vill.name.toLowerCase() === currentVillName.toLowerCase()) {
                                                opt.selected = true;
                                                const inputVill = document.getElementById('input_village');
                                                if (inputVill) inputVill.value = vill.name;
                                            }
                                            villSelect.appendChild(opt);
                                        });
                                    }
                                }
                            }
                        }
                    }
                } else {
                    console.warn(`No matching province found for: "${currentProvName}"`);
                }
            }
        } catch (error) {
            console.error('Error initializing address sections:', error);
        }
    }

    function handleProvinceChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const provId = el.value;
        const provName = selectedOption.dataset.name || '';
        const inputProv = document.getElementById('input_province');
        if (inputProv) inputProv.value = provName;

        const citySelect = document.getElementById('select_city');
        const distSelect = document.getElementById('select_district');
        const villSelect = document.getElementById('select_village');

        if (citySelect) {
            citySelect.innerHTML = '<option value="">Loading...</option>';
            citySelect.disabled = true;
        }
        if (distSelect) {
            distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            distSelect.disabled = true;
        }
        if (villSelect) {
            villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            villSelect.disabled = true;
        }

        const inputCity = document.getElementById('input_city');
        const inputDist = document.getElementById('input_district');
        const inputVill = document.getElementById('input_village');
        if (inputCity) inputCity.value = '';
        if (inputDist) inputDist.value = '';
        if (inputVill) inputVill.value = '';

        if (provId) {
            fetch(`${REGIONAL_API_BASE}/regencies/${provId}`)
                .then(response => response.json())
                .then(data => {
                    if (citySelect) {
                        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                        citySelect.disabled = false;
                        data.forEach(city => {
                            const opt = document.createElement('option');
                            opt.value = city.id;
                            opt.text = city.name;
                            opt.dataset.name = city.name;
                            citySelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching cities:', err);
                    if (citySelect) citySelect.innerHTML = '<option value="">Gagal Memuat</option>';
                });
        } else {
            if (citySelect) citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        }
    }

    function handleCityChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const cityId = el.value;
        const cityName = selectedOption.dataset.name || '';
        const inputCity = document.getElementById('input_city');
        if (inputCity) inputCity.value = cityName;

        const distSelect = document.getElementById('select_district');
        const villSelect = document.getElementById('select_village');

        if (distSelect) {
            distSelect.innerHTML = '<option value="">Loading...</option>';
            distSelect.disabled = true;
        }
        if (villSelect) {
            villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            villSelect.disabled = true;
        }

        const inputDist = document.getElementById('input_district');
        const inputVill = document.getElementById('input_village');
        if (inputDist) inputDist.value = '';
        if (inputVill) inputVill.value = '';

        if (cityId) {
            fetch(`${REGIONAL_API_BASE}/districts/${cityId}`)
                .then(response => response.json())
                .then(data => {
                    if (distSelect) {
                        distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                        distSelect.disabled = false;
                        data.forEach(dist => {
                            const opt = document.createElement('option');
                            opt.value = dist.id;
                            opt.text = dist.name;
                            opt.dataset.name = dist.name;
                            distSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching districts:', err);
                    if (distSelect) distSelect.innerHTML = '<option value="">Gagal Memuat</option>';
                });
        } else {
            if (distSelect) distSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        }
    }

    function handleDistrictChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const distId = el.value;
        const distName = selectedOption.dataset.name || '';
        const inputDist = document.getElementById('input_district');
        if (inputDist) inputDist.value = distName;

        const villSelect = document.getElementById('select_village');
        if (villSelect) {
            villSelect.innerHTML = '<option value="">Loading...</option>';
            villSelect.disabled = true;
        }
        const inputVill = document.getElementById('input_village');
        if (inputVill) inputVill.value = '';

        if (distId) {
            fetch(`${REGIONAL_API_BASE}/villages/${distId}`)
                .then(response => response.json())
                .then(data => {
                    if (villSelect) {
                        villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                        villSelect.disabled = false;
                        data.forEach(vill => {
                            const opt = document.createElement('option');
                            opt.value = vill.id;
                            opt.text = vill.name;
                            opt.dataset.name = vill.name;
                            villSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching villages:', err);
                    if (villSelect) villSelect.innerHTML = '<option value="">Gagal Memuat</option>';
                });
        } else {
            if (villSelect) villSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        }
    }

    function handleVillageChange(el) {
        const selectedOption = el.options[el.selectedIndex];
        const villName = selectedOption.dataset.name || '';
        const inputVill = document.getElementById('input_village');
        if (inputVill) inputVill.value = villName;
    }
    </script>
</x-app-layout>