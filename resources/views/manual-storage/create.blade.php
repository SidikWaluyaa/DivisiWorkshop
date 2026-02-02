<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Input Gudang Manual (Security Check)') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            
            <!-- Financial Security Alert -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Sistem ini menggunakan <strong>Financial-Driven Storage</strong>. Pilihan Rak akan dibatasi sesuai Status Pembayaran untuk mencegah kesalahan pengambilan barang (Release Error).
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <form action="{{ route('storage.manual.store') }}" method="POST" enctype="multipart/form-data" x-data="manualInput()">
                    @csrf

                    <!-- Section 1: Financial Security -->
                    <div class="mb-8 border-b pb-6 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="bg-red-100 text-red-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span>
                            Data Keuangan & SPK
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SPK Number -->
                            <div class="col-span-2">
                                <x-input-label for="spk_number" :value="__('Nomor SPK / Nota')" />
                                <x-text-input id="spk_number" class="block mt-1 w-full" type="text" name="spk_number" placeholder="Contoh: SPK-2024-001" required autofocus />
                            </div>

                            <!-- Payment Status -->
                            <div class="col-span-2">
                                <span class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Status Pembayaran <span class="text-red-500">*</span></span>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Tagih Lunas -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_status" value="tagih_lunas" x-model="status" class="peer sr-only" required>
                                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition text-center">
                                            <div class="font-bold text-red-700">Tagih Lunas (TL)</div>
                                            <div class="text-xs text-gray-500 mt-1">Belum Lunas. Wajib Bayar saat ambil.</div>
                                        </div>
                                    </label>
                                    
                                    <!-- Tagih Nanti -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_status" value="tagih_nanti" x-model="status" class="peer sr-only">
                                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50 transition text-center">
                                            <div class="font-bold text-yellow-700">Tagih Nanti (TN)</div>
                                            <div class="text-xs text-gray-500 mt-1">Invoice / Langganan bulanan.</div>
                                        </div>
                                    </label>

                                    <!-- Lunas -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_status" value="lunas" x-model="status" class="peer sr-only">
                                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition text-center">
                                            <div class="font-bold text-green-700">Lunas (L)</div>
                                            <div class="text-xs text-gray-500 mt-1">Sudah Lunas. Aman keluar.</div>
                                        </div>
                                    </label>

                                    <!-- Manual / Umum -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_status" value="manual" x-model="status" class="peer sr-only">
                                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-gray-500 peer-checked:bg-gray-50 hover:bg-gray-50 transition text-center">
                                            <div class="font-bold text-gray-700">Manual / Umum</div>
                                            <div class="text-xs text-gray-500 mt-1">Input Awal / Transit.</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Total Price -->
                            <div>
                                <x-input-label for="total_price" :value="__('Total Biaya Jasa (Rp)')" />
                                <x-text-input id="total_price" x-model="total_price" class="block mt-1 w-full" type="number" name="total_price" min="0" required />
                            </div>

                            <!-- Paid Amount -->
                            <div>
                                <x-input-label for="paid_amount" :value="__('Sudah Dibayar (Rp)')" />
                                <x-text-input id="paid_amount" x-model="paid_amount" class="block mt-1 w-full" type="number" name="paid_amount" min="0" required />
                            </div>

                            <!-- Remaining -->
                            <div class="col-span-2 bg-gray-100 dark:bg-gray-900 rounded-lg p-4 flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Sisa Tagihan:</span>
                                <span class="text-xl font-bold" :class="remaining > 0 ? 'text-red-600' : 'text-green-600'" x-text="formatRupiah(remaining)">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Storage Logic -->
                    <div class="mb-8 border-b pb-6 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="bg-red-100 text-red-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span>
                            Penempatan Barang
                        </h3>

                        <!-- Rack Selection -->
                        <div class="mb-6">
                            <label for="rack_code" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Pilih Rak <span class="text-red-500">*</span></label>
                            
                            <select id="rack_code" name="rack_code" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 p-3" required>
                                <option value="">-- Pilih Rak Sesuai Kategori --</option>
                                <template x-for="rack in filteredRacks" :key="rack.rack_code">
                                    <option :value="rack.rack_code" x-text="rackLabel(rack)"></option>
                                </template>
                            </select>
                            
                            <div x-show="!status" class="text-sm text-red-500 mt-2 animate-pulse">
                                &#9888; Silakan pilih Status Pembayaran di atas untuk membuka pilihan rak.
                            </div>
                             <div x-show="status && filteredRacks.length === 0" class="text-sm text-red-500 mt-2">
                                &#9888; Tidak ada rak tersedia untuk kategori ini. Hubungi Supervisor.
                            </div>
                        </div>

                        <!-- Item Name -->
                        <div class="mb-6">
                            <x-input-label for="item_name" :value="__('Nama Barang / Deskripsi Singkat')" />
                            <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" placeholder="Contoh: Helm Pelanggan, Kardus Sparepart" required />
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <x-input-label for="quantity" :value="__('Jumlah (Qty)')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" value="1" min="1" required />
                        </div>
                    </div>

                    <!-- Section 3: Evidence -->
                    <div class="mb-8">
                         <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="bg-red-100 text-red-600 w-6 h-6 rounded-full flex items-center justify-center text-xs">3</span>
                            Bukti & Detail
                        </h3>

                        <!-- Description -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Catatan Tambahan (Opsional)')" />
                            <textarea id="description" name="description" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 p-3" placeholder="Detail kondisi, alasan penyimpanan, dll..."></textarea>
                        </div>

                        <!-- Photo -->
                        <div class="mb-4">
                            <x-input-label for="photo" :value="__('Foto Barang')" />
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-500 transition cursor-pointer" onclick="document.getElementById('photo').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <span class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500">
                                            <span>Upload Foto</span>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                            <input id="photo" name="photo" type="file" class="sr-only" accept="image/*">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('storage.manual.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">Batal</a>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold shadow-lg transform active:scale-95 transition">
                            Simpan & Amankan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js Data -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('manualInput', () => ({
                status: '',
                total_price: 0,
                paid_amount: 0,
                racks: @json($racks),

                get remaining() {
                    return Math.max(0, this.total_price - this.paid_amount);
                },

                get filteredRacks() {
                    if (!this.status) return [];
                    
                    const targetCategory = {
                        'tagih_lunas': 'manual_tl',
                        'tagih_nanti': 'manual_tn',
                        'lunas': 'manual_l',
                        'manual': 'manual'
                    }[this.status];

                    return this.racks.filter(rack => {
                        // Handle both String and Enum Object serialization
                        let cat = rack.category;
                        if (typeof cat === 'object' && cat !== null) {
                            cat = cat.value || cat.name; 
                        }

                        if (cat === targetCategory) return true;
                        
                        // IF we want to allow General Manual racks to handle overflow:
                        if (cat === 'manual') return true;

                        return false; 
                    });
                },

                rackLabel(rack) {
                    let cat = rack.category;
                    if (typeof cat === 'object' && cat !== null) {
                        cat = cat.value || cat.name;
                    }

                    let catLabel = {
                        'manual': 'Umum',
                        'manual_tl': 'Tagih Lunas',
                        'manual_tn': 'Tagih Nanti',
                        'manual_l': 'Lunas'
                    }[cat] || cat;
                    
                    return `${rack.rack_code} [${catLabel}] (Sisa: ${rack.capacity - rack.current_count})`;
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
                }
            }));
        });
    </script>
</x-app-layout>
