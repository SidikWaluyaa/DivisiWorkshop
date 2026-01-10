<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Purchase Order Baru') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.purchases.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Supplier / Toko</label>
                            <input type="text" name="supplier_name" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" placeholder="Contoh: Toko Sepatu Jaya">
                            @error('supplier_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Material</label>
                            <select name="material_id" id="materialSelect" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                                <option value="">-- Pilih Material --</option>
                                @foreach($materials as $material)
                                <option value="{{ $material->id }}" data-price="{{ $material->price }}" data-unit="{{ $material->unit }}">
                                    {{ $material->name }} (Stock: {{ $material->stock }} {{ $material->unit }})
                                </option>
                                @endforeach
                            </select>
                            @error('material_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah</label>
                                <input type="number" name="quantity" id="quantity" min="1" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                                <p class="text-xs text-gray-500 mt-1">Unit: <span id="unitDisplay">-</span></p>
                                @error('quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Harga per Unit</label>
                                <input type="number" name="unit_price" id="unitPrice" step="0.01" min="0" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                                @error('unit_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Harga</div>
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="totalPrice">Rp 0</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Order</label>
                                <input type="date" name="order_date" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Jatuh Tempo</label>
                                <input type="date" name="due_date" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan</label>
                            <textarea name="notes" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg"></textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">
                                Buat Purchase Order
                            </button>
                            <a href="{{ route('admin.purchases.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-semibold text-center">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const materialSelect = document.getElementById('materialSelect');
        const quantityInput = document.getElementById('quantity');
        const unitPriceInput = document.getElementById('unitPrice');
        const unitDisplay = document.getElementById('unitDisplay');
        const totalPriceDisplay = document.getElementById('totalPrice');

        materialSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const price = selected.dataset.price;
            const unit = selected.dataset.unit;
            
            unitPriceInput.value = price;
            unitDisplay.textContent = unit;
            calculateTotal();
        });

        quantityInput.addEventListener('input', calculateTotal);
        unitPriceInput.addEventListener('input', calculateTotal);

        function calculateTotal() {
            const qty = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(unitPriceInput.value) || 0;
            const total = qty * price;
            
            totalPriceDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
</x-app-layout>
