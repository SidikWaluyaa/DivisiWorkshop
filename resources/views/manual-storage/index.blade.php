<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gudang Manual') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-red-100 text-sm font-medium mb-1">Total Item Tersimpan</div>
                    <div class="text-4xl font-bold">{{ number_format($totalItems) }}</div>
                    <div class="mt-2 text-xs text-red-200">Unit di Gudang Manual</div>
                </div>
                <div class="absolute right-0 bottom-0 opacity-10 transform translate-y-2 translate-x-2">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg text-blue-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Masuk Hari Ini</div>
                        <div class="text-2xl font-bold text-gray-800">{{ number_format($todayIn) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg text-green-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Keluar Hari Ini</div>
                        <div class="text-2xl font-bold text-gray-800">{{ number_format($todayOut) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions & Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ route('storage.manual.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Input Barang Manual
                </a>
                <a href="{{ route('storage.manual.history') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Keluar
                </a>
            </div>

            <form action="{{ route('storage.manual.index') }}" method="GET" class="flex flex-col md:flex-row gap-2 w-full md:w-auto flex-1 justify-end">
                <select name="rack_code" class="border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm" onchange="this.form.submit()">
                    <option value="">Semua Rak</option>
                    @foreach($racks as $rack)
                        <option value="{{ $rack->rack_code }}" {{ request('rack_code') == $rack->rack_code ? 'selected' : '' }}>
                            {{ $rack->rack_code }} ({{ $rack->category instanceof \BackedEnum ? $rack->category->value : $rack->category }})
                        </option>
                    @endforeach
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..." class="border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 pl-10 text-sm w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Foto</th>
                            <th class="px-6 py-3">Item & SPK</th>
                            <th class="px-6 py-3">Status Bayar</th>
                            <th class="px-6 py-3 text-right">Sisa Tagihan</th>
                            <th class="px-6 py-3">Rak</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 w-20">
                                @if($item->image_path)
                                    <img src="{{ Storage::url($item->image_path) }}" class="h-12 w-12 object-cover rounded-lg border border-gray-200 cursor-pointer" onclick="openImageModal(this.src)">
                                @else
                                    <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $item->spk_number ?? '-' }}</div>
                                <a href="{{ route('storage.manual.show', $item->id) }}" class="text-sm text-gray-600 hover:text-red-600 font-medium">
                                    {{ $item->item_name }} ({{ $item->quantity }})
                                </a>
                                @if($item->description)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $item->description }}</div>
                                @endif
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $item->in_date->format('d M H:i') }} | {{ $item->storer->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'lunas' => 'bg-green-100 text-green-800 border-green-200',
                                        'tagih_nanti' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'tagih_lunas' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $statusLabel = [
                                        'lunas' => 'Lunas',
                                        'tagih_nanti' => 'Tagih Nanti',
                                        'tagih_lunas' => 'Tagih Lunas',
                                    ];
                                    $color = $statusColors[$item->payment_status] ?? 'bg-gray-100 text-gray-800';
                                    $label = $statusLabel[$item->payment_status] ?? ucfirst($item->payment_status);
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $remaining = $item->total_price - $item->paid_amount;
                                @endphp
                                <div class="font-bold {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($remaining, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    Total: {{ number_format($item->total_price, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded border border-gray-200">
                                    {{ $item->rack_code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('storage.manual.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 p-1 bg-blue-50 rounded-lg" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    
                                    {{-- Release Button --}}
                                    <form action="{{ route('storage.manual.release', $item->id) }}" method="POST" onsubmit="return confirm('Keluarkan barang ini?')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 p-1 bg-green-50 rounded-lg" title="Keluarkan (Release)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-500">
                                Belum ada barang di gudang manual.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
