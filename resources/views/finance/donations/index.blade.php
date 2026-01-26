<x-app-layout>
    <div class="min-h-screen bg-gray-100 pb-12">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-800 to-red-600 shadow-xl">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-black text-white">Data Donasi / Hangus</h1>
                        <p class="text-red-100 text-sm mt-1">Daftar pesanan yang tidak diambil/dibayar dan statusnya dialihkan menjadi Donasi</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 -mt-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    @if($orders->isEmpty())
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Tidak Ada Data Donasi</h3>
                            <p class="text-gray-500 text-sm">Semua pesanan masih dalam masa aktif atau sudah lunas.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200 text-left">
                                        <th class="py-4 px-6 font-bold text-xs text-gray-400 uppercase tracking-wider">SPK & Customer</th>
                                        <th class="py-4 px-6 font-bold text-xs text-gray-400 uppercase tracking-wider">Item Sepatu</th>
                                        <th class="py-4 px-6 font-bold text-xs text-gray-400 uppercase tracking-wider text-right">Tagihan Hangus</th>
                                        <th class="py-4 px-6 font-bold text-xs text-gray-400 uppercase tracking-wider text-right">Tgl Donasi</th>
                                        <th class="py-4 px-6 font-bold text-xs text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-red-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-black text-gray-900">{{ $order->spk_number }}</div>
                                            <div class="text-xs text-gray-600 font-medium">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-gray-400 mt-1">{{ $order->customer_phone }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-sm font-bold text-gray-800">{{ $order->shoe_brand }} {{ $order->shoe_type }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_color }}</div>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="font-black text-red-600">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-gray-400">Total: Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ $order->donated_at ? $order->donated_at->format('d M Y') : '-' }}</div>
                                            <div class="text-[10px] text-gray-500">
                                                {{ $order->donated_at ? $order->donated_at->diffForHumans() : '' }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <form action="{{ route('finance.donations.restore', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan data ini ke status aktif?');">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 hover:bg-teal-50 hover:border-teal-300 hover:text-teal-700 rounded-lg text-xs font-bold text-gray-600 shadow-sm transition-all focus:outline-none">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    Restore
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 px-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
