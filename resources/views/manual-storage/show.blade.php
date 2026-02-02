<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Barang Manual') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="md:flex items-center mb-6 hidden">
                <a href="{{ route('storage.manual.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Barang Manual</h1>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header Status -->
                <div class="bg-gray-50 px-8 py-6 border-b flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-500 block uppercase tracking-wide font-semibold">Status Barang</span>
                        @if($item->status === 'stored')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mt-1">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Tersimpan (Stored)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 mt-1">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Sudah Diambil (Retrieved)
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 block uppercase tracking-wide font-semibold text-right">Lokasi Rak</span>
                        <div class="text-2xl font-bold text-gray-900 text-right">{{ $item->rack_code }}</div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Image -->
                        <div>
                            @if($item->image_path)
                                <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->item_name }}" class="w-full rounded-xl shadow-md border cursor-pointer" onclick="window.open(this.src, '_blank')">
                            @else
                                <div class="w-full h-64 bg-gray-100 rounded-xl flex flex-col items-center justify-center text-gray-400 border-2 border-dashed">
                                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Tidak ada foto</span>
                                </div>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Identitas Barang</h3>
                                <div class="text-xl font-bold text-gray-900">{{ $item->spk_number ?? 'No SPK' }}</div>
                                <p class="text-gray-600 text-lg">{{ $item->item_name }}</p>
                            </div>

                            <!-- Financial Box -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Data Keuangan</h3>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm text-gray-600">Status Pembayaran</span>
                                    @php
                                        $statusColors = [
                                            'lunas' => 'bg-green-100 text-green-800 border-green-200',
                                            'tagih_nanti' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'tagih_lunas' => 'bg-red-100 text-red-800 border-red-200',
                                        ];
                                        $color = $statusColors[$item->payment_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-bold border {{ $color }} uppercase">
                                        {{ ucfirst(str_replace('_', ' ', $item->payment_status)) }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Total Biaya</span>
                                        <span class="font-medium">Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Sudah Dibayar</span>
                                        <span class="font-medium text-green-600">Rp {{ number_format($item->paid_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 my-2 pt-2 flex justify-between items-center">
                                        <span class="font-bold text-gray-700">Sisa Tagihan</span>
                                        @php $remaining = $item->total_price - $item->paid_amount; @endphp
                                        <span class="font-bold text-lg {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            Rp {{ number_format($remaining, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jumlah (Qty)</h3>
                                    <p class="text-gray-600">{{ $item->quantity }} Unit</p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Catatan</h3>
                                    <p class="text-gray-600 text-sm">{{ $item->description ?? '-' }}</p>
                                </div>
                            </div>

                            <hr>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Disimpan Oleh</p>
                                    <p class="font-medium">{{ $item->storer->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->in_date->format('d M Y H:i') }}</p>
                                </div>
                                @if($item->out_date)
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Diambil Oleh</p>
                                    <p class="font-medium">{{ $item->retriever->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->out_date->format('d M Y H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    @if($item->status === 'stored')
                    <div class="mt-8 pt-6 border-t flex justify-between items-center">
                        <form action="{{ route('storage.manual.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus Permanen</button>
                        </form>
                        
                        <div class="flex space-x-3">
                             <a href="{{ route('storage.manual.edit', $item->id) }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">
                                Edit
                            </a>
                            <form action="{{ route('storage.manual.release', $item->id) }}" method="POST" onsubmit="return confirm('Keluarkan item ini dari gudang?');">
                                @csrf
                                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold shadow-lg transition flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                    Keluarkan Barang
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
