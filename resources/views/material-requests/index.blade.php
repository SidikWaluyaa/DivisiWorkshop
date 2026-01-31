<x-app-layout>
<div class="container-fluid px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold" style="color: #1f2937;">Pengajuan Material</h1>
        <p class="text-gray-600 mt-1">Kolam Belanja & Purchase Order</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('material-requests.index') }}" class="flex flex-wrap gap-4">
            {{-- Search --}}
            <div class="flex-1 min-w-[250px]">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nomor request atau nama..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-opacity-50"
                    style="focus:ring-color: #22AF85;"
                >
            </div>

            {{-- Type Filter --}}
            <div class="min-w-[180px]">
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-opacity-50" style="focus:ring-color: #22AF85;">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="SHOPPING" {{ request('type') == 'SHOPPING' ? 'selected' : '' }}>Belanja</option>
                    <option value="PRODUCTION_PO" {{ request('type') == 'PRODUCTION_PO' ? 'selected' : '' }}>PO Produksi</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="min-w-[180px]">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-opacity-50" style="focus:ring-color: #22AF85;">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                    <option value="PURCHASED" {{ request('status') == 'PURCHASED' ? 'selected' : '' }}>Purchased</option>
                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            {{-- Filter Button --}}
            <button type="submit" class="px-6 py-2 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #22AF85;">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            {{-- Reset Button --}}
            <a href="{{ route('material-requests.index') }}" class="px-6 py-2 rounded-lg font-medium border-2 transition-all duration-200 hover:shadow-md" style="border-color: #22AF85; color: #22AF85;">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
        </form>
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

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Requests List --}}
    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        {{-- Left Side: Request Info --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                {{-- Request Number --}}
                                <h3 class="text-xl font-bold" style="color: #1f2937;">
                                    {{ $request->request_number }}
                                </h3>

                                {{-- Type Badge --}}
                                @if($request->type === 'SHOPPING')
                                    <span class="px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: #FFC232;">
                                        <i class="fas fa-shopping-cart mr-1"></i>Belanja
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: #22AF85;">
                                        <i class="fas fa-box mr-1"></i>PO Produksi
                                    </span>
                                @endif

                                {{-- Status Badge --}}
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($request->status === 'PENDING') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'APPROVED') bg-green-100 text-green-800
                                    @elseif($request->status === 'REJECTED') bg-red-100 text-red-800
                                    @elseif($request->status === 'PURCHASED') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $request->status }}
                                </span>
                            </div>

                            {{-- Request Details --}}
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                                <div>
                                    <i class="fas fa-user mr-2" style="color: #22AF85;"></i>
                                    <span class="font-medium">Diminta oleh:</span>
                                    <p class="ml-6">{{ $request->requestedBy->name ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <i class="fas fa-calendar mr-2" style="color: #22AF85;"></i>
                                    <span class="font-medium">Tanggal:</span>
                                    <p class="ml-6">{{ $request->created_at->format('d M Y') }}</p>
                                </div>

                                @if($request->work_order_id)
                                    <div>
                                        <i class="fas fa-file-alt mr-2" style="color: #22AF85;"></i>
                                        <span class="font-medium">Work Order:</span>
                                        <p class="ml-6">{{ $request->workOrder->spk_number ?? 'N/A' }}</p>
                                    </div>
                                @endif

                                @if($request->oto_id)
                                    <div>
                                        <i class="fas fa-sync-alt mr-2" style="color: #22AF85;"></i>
                                        <span class="font-medium">OTO:</span>
                                        <p class="ml-6">{{ $request->oto->oto_number ?? 'N/A' }}</p>
                                    </div>
                                @endif

                                <div>
                                    <i class="fas fa-money-bill-wave mr-2" style="color: #FFC232;"></i>
                                    <span class="font-medium">Estimasi:</span>
                                    <p class="ml-6 font-bold" style="color: #FFC232;">Rp {{ number_format($request->total_estimated_cost, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- Items Count --}}
                            <div class="mt-3 text-sm text-gray-600">
                                <i class="fas fa-list mr-2"></i>
                                <span class="font-medium">{{ $request->items->count() }} item(s)</span>
                            </div>

                            {{-- Notes --}}
                            @if($request->notes)
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg text-sm text-gray-700">
                                    <i class="fas fa-sticky-note mr-2 text-gray-500"></i>
                                    <span class="font-medium">Catatan:</span> {{ $request->notes }}
                                </div>
                            @endif

                            {{-- Approval Info --}}
                            @if($request->approved_by)
                                <div class="mt-3 text-sm text-gray-600">
                                    <i class="fas fa-check-circle mr-2" style="color: #22AF85;"></i>
                                    <span class="font-medium">Disetujui oleh:</span> {{ $request->approvedBy->name ?? 'N/A' }}
                                    <span class="ml-2">pada {{ $request->approved_at->format('d M Y H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Right Side: Actions --}}
                        <div class="flex flex-col gap-2 ml-4">
                            <a href="{{ route('material-requests.show', $request) }}" class="px-4 py-2 rounded-lg font-medium text-white text-center transition-all duration-200 hover:shadow-lg" style="background-color: #22AF85;">
                                <i class="fas fa-eye mr-2"></i>Detail
                            </a>

                            @if($request->status === 'PENDING')
                                @can('manageInventory')
                                <form action="{{ route('material-requests.approve', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #22AF85;" onclick="return confirm('Setujui pengajuan ini?')">
                                        <i class="fas fa-check mr-2"></i>Approve
                                    </button>
                                </form>

                                <form action="{{ route('material-requests.reject', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-red-500 rounded-lg font-medium text-white transition-all duration-200 hover:bg-red-600 hover:shadow-lg" onclick="return confirm('Tolak pengajuan ini?')">
                                        <i class="fas fa-times mr-2"></i>Reject
                                    </button>
                                </form>
                                @endcan
                            @endif

                            @if($request->status === 'APPROVED')
                                @can('manageInventory')
                                <form action="{{ route('material-requests.mark-purchased', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 rounded-lg font-medium text-white transition-all duration-200 hover:shadow-lg" style="background-color: #FFC232;" onclick="return confirm('Tandai sebagai sudah dibeli?')">
                                        <i class="fas fa-shopping-bag mr-2"></i>Mark Purchased
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Tidak ada pengajuan material</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @endif
</div>

<style>
    /* Custom focus ring color */
    input:focus, select:focus {
        outline: none;
        ring-color: #22AF85;
        border-color: #22AF85;
    }
</style>
</x-app-layout>
