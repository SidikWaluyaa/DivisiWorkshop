<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Purchase Order') }} - {{ $purchase->po_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.purchases.update', $purchase) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Supplier / Toko</label>
                            <input type="text" name="supplier_name" value="{{ $purchase->supplier_name }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" placeholder="Contoh: Toko Sepatu Jaya">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Material</label>
                            <select name="material_id" id="materialSelect" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                                @foreach($materials as $material)
                                <option value="{{ $material->id }}" 
                                    data-price="{{ $material->price }}" 
                                    data-unit="{{ $material->unit }}"
                                    {{ $purchase->material_id == $material->id ? 'selected' : '' }}>
                                    {{ $material->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
                                <input type="number" name="quantity" id="quantity" value="{{ $purchase->quantity }}" min="1" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Harga per Unit</label>
                                <input type="number" name="unit_price" id="unitPrice" value="{{ $purchase->unit_price }}" step="0.01" min="0" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Harga</div>
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="totalPrice">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select name="status" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg" required>
                                    <option value="pending" {{ $purchase->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ordered" {{ $purchase->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                    <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received (Stock akan otomatis bertambah)</option>
                                    <option value="cancelled" {{ $purchase->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @if($purchase->status === 'received')
                                <p class="text-xs text-green-600 mt-1">✓ Barang sudah diterima pada {{ $purchase->received_date?->format('d M Y') }}</p>
                                @endif
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ratting Kualitas (1-5)</label>
                                <select name="quality_rating" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                                    <option value="">-- Beri Penilaian --</option>
                                    @for($i=1; $i<=5; $i++)
                                        <option value="{{ $i }}" {{ $purchase->quality_rating == $i ? 'selected' : '' }}>{{ $i }} Bintang {{ str_repeat('⭐', $i) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Order</label>
                                <input type="date" name="order_date" value="{{ $purchase->order_date?->format('Y-m-d') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Jatuh Tempo</label>
                                <input type="date" name="due_date" value="{{ $purchase->due_date?->format('Y-m-d') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan</label>
                            <textarea name="notes" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg">{{ $purchase->notes }}</textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">
                                Update Purchase Order
                            </button>
                            <a href="{{ route('admin.purchases.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-semibold text-center">
                                Batal
                            </a>
                        </div>
                    </form>

                    @if($purchase->status !== 'received')
                    <form action="{{ route('admin.purchases.destroy', $purchase) }}" method="POST" class="mt-4" onsubmit="return confirm('Yakin ingin hapus PO ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">
                            Hapus Purchase Order
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        const quantityInput = document.getElementById('quantity');
        const unitPriceInput = document.getElementById('unitPrice');
        const totalPriceDisplay = document.getElementById('totalPrice');

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
