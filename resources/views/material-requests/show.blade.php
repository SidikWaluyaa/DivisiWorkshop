<x-app-layout>
<div class="container-fluid px-4 py-6">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold" style="color: #1f2937;">Detail Pengajuan Material</h1>
            <p class="text-gray-600 mt-1">{{ $materialRequest->request_number }}</p>
        </div>

        <a href="{{ route('material-requests.index') }}" class="px-4 py-2 border-2 rounded-lg font-medium transition-all duration-200 hover:shadow-md" style="border-color: #22AF85; color: #22AF85;">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 p-4 mb-6 rounded-lg" style="border-color: #22AF85;">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3" style="color: #22AF85;"></i>
                <p class="font-medium" style="color: #22AF85;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Request Info Card --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4" style="color: #1f2937;">Informasi Pengajuan</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nomor Request</p>
                        <p class="font-bold text-lg" style="color: #22AF85;">{{ $materialRequest->request_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tipe</p>
                        @if($materialRequest->type === 'SHOPPING')
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: #FFC232;">
                                <i class="fas fa-shopping-cart mr-1"></i>Belanja
                            </span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: #22AF85;">
                                <i class="fas fa-box mr-1"></i>PO Produksi
                            </span>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                            @if($materialRequest->status === 'PENDING') bg-yellow-100 text-yellow-800
                            @elseif($materialRequest->status === 'APPROVED') bg-green-100 text-green-800
                            @elseif($materialRequest->status === 'REJECTED') bg-red-100 text-red-800
                            @elseif($materialRequest->status === 'PURCHASED') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $materialRequest->status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tanggal Pengajuan</p>
                        <p class="font-medium">{{ $materialRequest->created_at->format('d M Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Diminta Oleh</p>
                        <p class="font-medium">{{ $materialRequest->requestedBy->name ?? 'N/A' }}</p>
                    </div>

                    @if($materialRequest->work_order_id)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Work Order</p>
                            <p class="font-medium">{{ $materialRequest->workOrder->spk_number ?? 'N/A' }}</p>
                        </div>
                    @endif

                    @if($materialRequest->oto_id)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">OTO</p>
                            <p class="font-medium">{{ $materialRequest->oto->oto_number ?? 'N/A' }}</p>
                        </div>
                    @endif

                    @if($materialRequest->approved_by)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Disetujui Oleh</p>
                            <p class="font-medium">{{ $materialRequest->approvedBy->name ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tanggal Approval</p>
                            <p class="font-medium">{{ $materialRequest->approved_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                @if($materialRequest->notes)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Catatan</p>
                        <p class="text-gray-800">{{ $materialRequest->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Items List --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4" style="color: #1f2937;">Daftar Material</h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2" style="border-color: #22AF85;">
                                <th class="text-left py-3 px-2 font-semibold" style="color: #1f2937;">#</th>
                                <th class="text-left py-3 px-2 font-semibold" style="color: #1f2937;">Nama Material</th>
                                <th class="text-left py-3 px-2 font-semibold" style="color: #1f2937;">Spesifikasi</th>
                                <th class="text-right py-3 px-2 font-semibold" style="color: #1f2937;">Qty</th>
                                <th class="text-right py-3 px-2 font-semibold" style="color: #1f2937;">Harga</th>
                                <th class="text-right py-3 px-2 font-semibold" style="color: #1f2937;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialRequest->items as $index => $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-2 text-gray-600">{{ $index + 1 }}</td>
                                    <td class="py-3 px-2">
                                        <p class="font-medium text-gray-800">{{ $item->material_name }}</p>
                                        @if($item->isCustomMaterial())
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">Custom</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 text-gray-600 text-sm">{{ $item->specification ?? '-' }}</td>
                                    <td class="py-3 px-2 text-right font-medium">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="py-3 px-2 text-right">Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</td>
                                    <td class="py-3 px-2 text-right font-bold" style="color: #FFC232;">
                                        Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($item->notes)
                                    <tr class="border-b border-gray-100">
                                        <td colspan="6" class="py-2 px-2">
                                            <div class="bg-yellow-50 p-2 rounded text-sm text-gray-700">
                                                <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                                                {{ $item->notes }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2" style="border-color: #22AF85;">
                                <td colspan="5" class="py-4 px-2 text-right font-bold text-lg" style="color: #1f2937;">Total Estimasi:</td>
                                <td class="py-4 px-2 text-right font-bold text-xl" style="color: #FFC232;">
                                    Rp {{ number_format($materialRequest->total_estimated_cost, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar: Actions --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h3 class="text-lg font-bold mb-4" style="color: #1f2937;">Aksi</h3>

                <div class="space-y-3">
                    @if($materialRequest->status === 'PENDING')
                        <form action="{{ route('material-requests.approve', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #22AF85;" onclick="return confirm('Setujui pengajuan ini?')">
                                <i class="fas fa-check mr-2"></i>Approve Pengajuan
                            </button>
                        </form>

                        <form action="{{ route('material-requests.reject', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-red-500 rounded-lg font-medium text-white transition-all duration-200 hover:bg-red-600 hover:shadow-lg" onclick="return confirm('Tolak pengajuan ini?')">
                                <i class="fas fa-times mr-2"></i>Reject Pengajuan
                            </button>
                        </form>

                        <form action="{{ route('material-requests.cancel', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gray-500 rounded-lg font-medium text-white transition-all duration-200 hover:bg-gray-600 hover:shadow-lg" onclick="return confirm('Batalkan pengajuan ini?')">
                                <i class="fas fa-ban mr-2"></i>Cancel
                            </button>
                        </form>
                    @endif

                    @if($materialRequest->status === 'APPROVED')
                        <form action="{{ route('material-requests.mark-purchased', $materialRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #FFC232;" onclick="return confirm('Tandai sebagai sudah dibeli?')">
                                <i class="fas fa-shopping-bag mr-2"></i>Mark as Purchased
                            </button>
                        </form>
                    @endif

                    @if($materialRequest->status === 'PURCHASED')
                        <div class="p-4 bg-blue-50 rounded-lg text-center">
                            <i class="fas fa-check-circle text-3xl text-blue-500 mb-2"></i>
                            <p class="text-blue-700 font-medium">Material Sudah Dibeli</p>
                        </div>
                    @endif

                    @if($materialRequest->status === 'REJECTED')
                        <div class="p-4 bg-red-50 rounded-lg text-center">
                            <i class="fas fa-times-circle text-3xl text-red-500 mb-2"></i>
                            <p class="text-red-700 font-medium">Pengajuan Ditolak</p>
                        </div>
                    @endif

                    @if($materialRequest->status === 'CANCELLED')
                        <div class="p-4 bg-gray-50 rounded-lg text-center">
                            <i class="fas fa-ban text-3xl text-gray-500 mb-2"></i>
                            <p class="text-gray-700 font-medium">Pengajuan Dibatalkan</p>
                        </div>
                    @endif
                </div>

                {{-- Info Box --}}
                <div class="mt-6 p-4 rounded-lg" style="background-color: #f0fdf4; border-left: 4px solid #22AF85;">
                    <h4 class="font-bold mb-2" style="color: #22AF85;">
                        <i class="fas fa-info-circle mr-2"></i>Informasi
                    </h4>
                    <ul class="text-sm text-gray-700 space-y-1">
                        @if($materialRequest->type === 'SHOPPING')
                            <li>• Material belanja tidak tergantung stok</li>
                            <li>• Setelah approved, proses pembelian</li>
                            <li>• Update stok setelah barang datang</li>
                        @else
                            <li>• PO untuk kekurangan stok produksi</li>
                            <li>• Setelah approved, proses pembelian</li>
                            <li>• Material bisa digunakan setelah purchased</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
