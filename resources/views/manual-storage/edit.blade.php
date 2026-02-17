<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Item Manual') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="md:flex items-center mb-6 hidden">
                <a href="{{ route('storage.manual.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Edit Item Manual</h1>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8" x-data="manualInput">
                <form action="{{ route('storage.manual.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                <x-text-input id="spk_number" class="block mt-1 w-full" type="text" name="spk_number" value="{{ $item->spk_number }}" placeholder="Contoh: SPK-2024-001" required />
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
                                            <div class="text-xs text-gray-500 mt-1">Belum Lunas.</div>
                                        </div>
                                    </label>
                                    
                                    <!-- Tagih Nanti -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_status" value="tagih_nanti" x-model="status" class="peer sr-only">
                                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50 transition text-center">
                                            <div class="font-bold text-yellow-700">Tagih Nanti (TN)</div>
                                            <div class="text-xs text-gray-500 mt-1">Invoice / Langganan.</div>
                                        </div>
                                    </label>

                                    <!-- Lunas -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_status" value="lunas" x-model="status" class="peer sr-only">
                                        <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition text-center">
                                            <div class="font-bold text-green-700">Lunas (L)</div>
                                            <div class="text-xs text-gray-500 mt-1">Sudah Lunas.</div>
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
                            
                            <select id="rack_code" name="rack_code" x-model="selectedRack" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 p-3" required>
                                <option value="">-- Pilih Rak Sesuai Kategori --</option>
                                <template x-for="rack in filteredRacks" :key="rack.rack_code">
                                    <option :value="rack.rack_code" x-text="rackLabel(rack)"></option>
                                </template>
                            </select>
                            
                             <div x-show="status && filteredRacks.length === 0" class="text-sm text-red-500 mt-2">
                                &#9888; Tidak ada rak tersedia untuk kategori ini. Hubungi Supervisor.
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Jika mengubah status pembayaran, mohon pindahkan barang ke rak yang sesuai jika diperlukan.</p>
                        </div>

                        <!-- Item Name -->
                        <div class="mb-6">
                            <x-input-label for="item_name" :value="__('Nama Barang')" />
                            <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" value="{{ $item->item_name }}" required />
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <x-input-label for="quantity" :value="__('Jumlah (Qty)')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" value="{{ $item->quantity }}" min="1" required />
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
                            <x-input-label for="description" :value="__('Catatan')" />
                            <textarea id="description" name="description" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 p-3">{{ $item->description }}</textarea>
                        </div>

                        <!-- Current Photo -->
                        @if($item->image_path)
                        <div class="mb-4">
                            <p class="block text-gray-700 font-bold mb-2">Foto Saat Ini</p>
                            <img src="{{ $item->image_url }}" class="h-32 rounded-lg border">
                        </div>
                        @endif

                        <!-- Update Photo -->
                        <div class="mb-4">
                            <x-input-label for="photo" :value="__('Ganti Foto (Opsional)')" />
                            <input id="photo" type="file" name="photo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('storage.manual.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">Batal</a>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold shadow-lg">
                            Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('manualInput', () => ({
                status: {{ \Illuminate\Support\Js::from(old('payment_status', $item->payment_status)) }},
                total_price: {{ old('total_price', $item->total_price ?? 0) }},
                paid_amount: {{ old('paid_amount', $item->paid_amount ?? 0) }},
                selectedRack: {{ \Illuminate\Support\Js::from(old('rack_code', $item->rack_code)) }},
                racks: {{ \Illuminate\Support\Js::from($racks) }},

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
                        let cat = rack.category;
                        if (typeof cat === 'object' && cat !== null) {
                            cat = cat.value || cat.name; 
                        }

                        if (cat === targetCategory) return true;
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
                    
                    // Add safety check for numeric values
                    const capacity = Number(rack.capacity) || 0;
                    const current = Number(rack.current_count) || 0;
                    
                    return `${rack.rack_code} [${catLabel}] (Sisa: ${capacity - current})`;
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
                }
            }));
        });
    </script>
</x-app-layout>
