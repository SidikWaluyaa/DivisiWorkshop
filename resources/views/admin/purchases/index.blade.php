<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Purchase Management') }}
            </h2>
            <a href="{{ route('admin.purchases.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                + Buat PO Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pending Orders</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['total_pending'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Ordered</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_ordered'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Unpaid</div>
                    <div class="text-2xl font-bold text-red-600">Rp {{ number_format($stats['total_unpaid'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Outstanding</div>
                    <div class="text-2xl font-bold text-orange-600">Rp {{ number_format($stats['total_outstanding'], 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Purchases Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">PO Number</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Material</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Payment</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($purchases as $purchase)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="font-mono text-sm font-semibold">{{ $purchase->po_number }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $purchase->material->name }}</div>
                                        <div class="text-xs text-gray-500">@ Rp {{ number_format($purchase->unit_price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        {{ $purchase->quantity }} {{ $purchase->material->unit }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold">
                                        Rp {{ number_format($purchase->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($purchase->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($purchase->status === 'ordered') bg-blue-100 text-blue-800
                                            @elseif($purchase->status === 'received') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($purchase->payment_status === 'paid') bg-green-100 text-green-800
                                            @elseif($purchase->payment_status === 'partial') bg-orange-100 text-orange-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($purchase->payment_status) }}
                                        </span>
                                        @if($purchase->payment_status !== 'paid')
                                        <div class="text-xs text-gray-500 mt-1">
                                            Sisa: Rp {{ number_format($purchase->outstanding_amount, 0, ',', '.') }}
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            @if($purchase->payment_status !== 'paid')
                                            <button onclick="openPaymentModal({{ $purchase->id }}, {{ $purchase->outstanding_amount }})" class="text-green-600 hover:text-green-900">Bayar</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">
                                        Belum ada purchase order
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-bold mb-4">Bayar Purchase Order</h3>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Jumlah Pembayaran</label>
                    <input type="number" name="paid_amount" id="paidAmount" step="0.01" class="w-full border-gray-300 rounded-lg" required>
                    <p class="text-xs text-gray-500 mt-1">Sisa hutang: <span id="outstandingAmount"></span></p>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Bayar
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPaymentModal(purchaseId, outstanding) {
            document.getElementById('paymentForm').action = `/admin/purchases/${purchaseId}/payment`;
            document.getElementById('paidAmount').max = outstanding;
            document.getElementById('outstandingAmount').textContent = 'Rp ' + outstanding.toLocaleString('id-ID');
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
