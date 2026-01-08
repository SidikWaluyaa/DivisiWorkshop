<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cek Material: ') . $order->spk_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- LEFT COLUMN: Materials for this Order -->
                <div class="md:col-span-2 space-y-6">
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <h3 class="text-lg font-bold mb-4 dark:text-gray-100">Daftar Material Sepatu Ini</h3>
                        
                        <table class="w-full text-sm text-left text-gray-500 mb-6">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3 w-10">#</th>
                                    <th class="px-6 py-3">Material</th>
                                    <th class="px-6 py-3">Qty</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->materials as $index => $m)
                                <tr class="bg-white border-b dark:bg-gray-800">
                                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $m->name }}</td>
                                    <td class="px-6 py-4">{{ $m->pivot->quantity }} {{ $m->unit }}</td>
                                    <td class="px-6 py-4">
                                        @if($m->pivot->status == 'ALLOCATED')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">ALLOCATED</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">REQUESTED</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('sortir.destroy-material', ['id' => $order->id, 'materialId' => $m->id]) }}" method="POST" onsubmit="return confirm('Hapus material ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-900 font-bold">🗑️ Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center italic">Belum ada material yang ditambahkan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <form action="{{ route('sortir.add-material', $order->id) }}" method="POST" class="border-t pt-4 dark:border-gray-700">
                            @csrf
                            <div class="grid grid-cols-12 gap-4 items-end">
                                <div class="col-span-7">
                                    <x-input-label for="material_id" :value="__('Ambil dari Gudang')" />
                                    <select id="material_id" name="material_id" class="block mt-1 w-full border-gray-300 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        @foreach($allMaterials as $am)
                                            <option value="{{ $am->id }}" {{ $am->stock <= 0 ? 'disabled' : '' }}>
                                                {{ $am->name }} (Sisa: {{ $am->stock }} {{ $am->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <x-input-label for="quantity" :value="__('Qty')" />
                                    <input id="quantity" name="quantity" type="number" min="1" value="1" class="block mt-1 w-full border-gray-300 dark:bg-gray-900 rounded-md" />
                                </div>
                                <div class="col-span-2">
                                    <button class="w-full bg-indigo-600 text-white py-2 rounded shadow hover:bg-indigo-700 h-10 font-bold">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Live Warehouse Stock -->
                <div class="md:col-span-1">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 shadow sm:rounded-lg border border-gray-200 dark:border-gray-600">
                        <h3 class="text-md font-bold mb-4 text-gray-700 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Gudang Material
                        </h3>
                        <div class="space-y-3">
                            @foreach($allMaterials as $stock)
                                <div class="flex justify-between items-center text-sm p-2 bg-white dark:bg-gray-800 rounded shadow-sm">
                                    <span class="font-medium dark:text-gray-300">{{ $stock->name }}</span>
                                    <span class="{{ $stock->stock > 0 ? 'text-green-600' : 'text-red-600 font-bold' }}">
                                        {{ $stock->stock }} {{ $stock->unit }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-xs text-gray-500 text-center">
                            *Stok berkurang otomatis saat di-Add.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Action -->
            <div class="flex justify-end">
                @if($order->materials->where('pivot.status', 'REQUESTED')->count() > 0)
                    <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mr-4">
                        ⚠️ Tidak bisa lanjut. Ada material status REQUESTED (Stok Kurang).
                    </div>
                @else
                    <form action="{{ route('sortir.finish', $order->id) }}" method="POST" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PIC Material Sol (Opsional)</label>
                                <select name="pic_sortir_sol_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full">
                                    <option value="">-- Pilih PIC --</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PIC Material Upper (Opsional)</label>
                                <select name="pic_sortir_upper_id" class="text-sm border-gray-300 rounded dark:bg-gray-900 w-full">
                                    <option value="">-- Pilih PIC --</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg">
                            MATERIAL READY → KIRIM KE PRODUCTION
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
