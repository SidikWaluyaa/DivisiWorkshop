<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Barang Keluar') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="md:flex items-center mb-6 hidden">
            <a href="{{ route('storage.manual.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Barang Keluar</h1>
        </div>

        <!-- Filter -->
        <div class="bg-white p-4 rounded-xl shadow-sm mb-6">
            <form action="{{ route('storage.manual.history') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..." class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="w-full md:w-48">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="w-full md:w-48">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                </div>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- Bulk Delete Form -->
        <form action="{{ route('storage.manual.bulk-destroy') }}" method="POST" x-data="{ selected: [] }" id="bulkDeleteForm">
            @csrf
            @method('DELETE')

            <!-- Actions Bar -->
            <div class="mb-4 flex justify-between items-center" x-show="selected.length > 0" x-transition.opacity x-cloak>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-700">
                        <span x-text="selected.length"></span> item dipilih
                    </span>
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus item terpilih?')" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Terpilih
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 w-10">
                                <input type="checkbox"
                                       @change="selected = $el.checked ? [{{ $items->pluck('id')->join(',') }}] : []"
                                       class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            </th>
                            <th class="py-3 px-6">Foto</th>
                            <th class="py-3 px-6">Item & SPK</th>
                            <th class="py-3 px-6">Status Bayar</th>
                            <th class="py-3 px-6">Lokasi</th>
                            <th class="py-3 px-6">Keluar Pada</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @forelse($items as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 bg-white" :class="{'bg-red-50': selected.includes({{ $item->id }})}">
                            <td class="py-3 px-6">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}" x-model="selected"
                                       class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            </td>
                            <td class="py-3 px-6">
                                @if($item->image_path)
                                    <img src="{{ Storage::url($item->image_path) }}" class="w-10 h-10 rounded object-cover border cursor-pointer" onclick="window.open(this.src, '_blank')">
                                @else
                                   <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                   </div>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                <div class="font-bold text-gray-800">{{ $item->spk_number ?? '-' }}</div>
                                <div class="font-medium text-gray-900">{{ $item->item_name }}</div>
                                @if($item->description)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                @php
                                    $statusColors = [
                                        'lunas' => 'bg-green-100 text-green-800 border-green-200',
                                        'tagih_nanti' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'tagih_lunas' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $color = $statusColors[$item->payment_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs font-bold border {{ $color }}">
                                    {{ ucfirst($item->payment_status) }}
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <span class="bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs font-bold">{{ $item->rack_code }}</span>
                            </td>
                            <td class="py-3 px-6">
                                <div class="text-gray-800">{{ $item->out_date->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-500">
                                    Simpan: {{ $item->storer->name ?? '-' }} <br>
                                    Ambil: {{ $item->retriever->name ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <a href="{{ route('storage.manual.show', $item->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm border border-blue-200 bg-blue-50 px-3 py-1 rounded-lg">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                         <tr>
                            <td colspan="8" class="text-center py-8 text-gray-400">
                                Tidak ada riwayat barang.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    </div>
</x-app-layout>
