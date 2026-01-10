<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Material Validation') }}
                </h2>
                <div class="text-xs font-medium opacity-90 flex items-center gap-2">
                    <span class="bg-white/20 px-2 py-0.5 rounded text-white font-mono">
                        {{ $order->spk_number }}
                    </span>
                    <span>{{ $order->customer_name }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- LEFT COLUMN: Materials for this Order -->
                <div class="md:col-span-2 space-y-6">
                    <div class="dashboard-card overflow-hidden">
                        <div class="dashboard-card-header flex justify-between items-center">
                            <h3 class="dashboard-card-title">
                                📦 Daftar Material Sepatu Ini
                            </h3>
                            <span class="text-xs text-gray-400 font-mono">
                                Total Items: {{ $order->materials->count() }}
                            </span>
                        </div>
                        
                        <div class="dashboard-card-body p-0">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 w-10">#</th>
                                            <th class="px-6 py-3">Category</th>
                                            <th class="px-6 py-3">Material</th>
                                            <th class="px-6 py-3">Qty</th>
                                            <th class="px-6 py-3 text-center">Status</th>
                                            <th class="px-6 py-3 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($order->materials as $index => $m)
                                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 font-mono text-xs">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide
                                                    {{ str_contains(strtolower($m->category), 'sol') ? 'bg-orange-100 text-orange-800' : 
                                                       (str_contains(strtolower($m->category), 'upper') ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600') }}">
                                                    {{ $m->category }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 font-bold text-gray-800">{{ $m->name }}</td>
                                            <td class="px-6 py-4">{{ $m->pivot->quantity }} <span class="text-xs text-gray-400">{{ $m->unit }}</span></td>
                                            <td class="px-6 py-4 text-center">
                                                @if($m->pivot->status == 'ALLOCATED')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                        ✅ ALLOCATED
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 animate-pulse">
                                                        ⚠️ REQUESTED
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <form action="{{ route('sortir.destroy-material', ['id' => $order->id, 'materialId' => $m->id]) }}" method="POST" onsubmit="return confirm('Hapus material ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus Material">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic bg-gray-50/50">
                                                Belum ada material yang ditambahkan untuk sepatu ini.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="p-6 bg-gray-50 border-t border-gray-100">
                             <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Ambil Material dari Gudang
                            </h4>
                            <form action="{{ route('sortir.add-material', $order->id) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-12 gap-3 items-end">
                                    <div class="col-span-8 sm:col-span-8">
                                        <select id="material_id" name="material_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm transition-all bg-white" required>
                                            <option value="">-- Pilih Material --</option>
                                            @foreach($allMaterials as $am)
                                                <option value="{{ $am->id }}" {{ $am->stock <= 0 ? 'disabled' : '' }} class="{{ $am->stock <= 0 ? 'text-gray-300' : '' }}">
                                                    [{{ $am->category }}] {{ $am->name }} (Sisa: {{ $am->stock }} {{ $am->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-4 sm:col-span-2">
                                        <input id="quantity" name="quantity" type="number" min="1" value="1" placeholder="Qty" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm text-center font-bold" />
                                    </div>
                                    <div class="col-span-12 sm:col-span-2">
                                        <button class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-lg shadow hover:shadow-md transition-all font-bold text-sm tracking-wide">
                                            AMBIL
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Live Warehouse Stock -->
                <div class="md:col-span-1">
                    <div class="dashboard-card h-full">
                        <div class="dashboard-card-header bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                            <h3 class="dashboard-card-title text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Gudang Material
                            </h3>
                        </div>
                        <div class="p-4 bg-gray-50 h-[400px] overflow-y-auto custom-scrollbar">
                            <div class="space-y-2">
                                @foreach($allMaterials as $stock)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 hover:border-teal-200 transition-colors shadow-sm group">
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700 transition-colors">{{ $stock->name }}</span>
                                        <span class="text-xs font-mono font-bold px-2 py-1 rounded {{ $stock->stock > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $stock->stock }} {{ $stock->unit }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-3 bg-white border-t border-gray-100 text-xs text-center text-gray-500 italic">
                            *Stok berkurang otomatis saat di-Ambil.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Action -->
            <div class="dashboard-card p-6">
                @if($order->materials->where('pivot.status', 'REQUESTED')->count() > 0)
                    <div class="flex items-center gap-4 bg-red-50 border border-red-100 p-4 rounded-xl text-red-800">
                        <div class="p-3 bg-red-100 rounded-full shrink-0 animate-pulse">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Validasi Tertunda</h4>
                            <p class="text-sm">Ada material dengan status <span class="font-bold bg-white px-1 rounded text-red-600 border border-red-200">REQUESTED</span> (Stok Kurang). Mohon lakukan pembelanjaan / restock terlebih dahulu.</p>
                        </div>
                    </div>
                @else
                    @php
                        $hasSol = $order->materials->contains(fn($m) => str_contains(strtolower($m->category), 'sol'));
                        $hasUpper = $order->materials->contains(fn($m) => str_contains(strtolower($m->category), 'upper'));
                        // If no materials or only general, maybe show both optional? 
                        // Or adhere strictly:
                        // "Untuk PIC muncul atau boleh diisi tergantung material yang dipakai"
                        // If no specific material, maybe don't show PIC? Or show both as optional?
                        // Let's assume if hasSol -> Show Sol PIC (Required?). 
                        // User said "boleh diisi", implies optional but visible.
                        // I will show them if relevant material exists.
                    @endphp

                    <form action="{{ route('sortir.finish', $order->id) }}" method="POST">
                        @csrf
                        <div class="flex flex-col md:flex-row items-end gap-6 justify-end">
                            
                            @if($hasSol)
                            <div class="w-full md:w-1/3">
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">PIC Material Sol (Wajib)</label>
                                <select name="pic_sortir_sol_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 py-3 shadow-sm bg-orange-50/50" required>
                                    <option value="">-- Pilih PIC (Sol) --</option>
                                    @foreach($techSol as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if($hasUpper)
                            <div class="w-full md:w-1/3">
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">PIC Material Upper (Wajib)</label>
                                <select name="pic_sortir_upper_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 py-3 shadow-sm bg-purple-50/50" required>
                                    <option value="">-- Pilih PIC (Upper) --</option>
                                    @foreach($techUpper as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            
                            <div class="w-full md:w-1/3">
                                <button class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-black rounded-xl shadow-lg transform hover:-translate-y-0.5 transition-all text-sm uppercase tracking-wider">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Material Ready → Production
                                </button>
                            </div>
                        </div>
                        @if(!$hasSol && !$hasUpper)
                            <div class="mt-4 text-xs text-gray-400 italic text-center">
                                Tidak ada material spesifik Sol/Upper. Langsung lanjut ke produksi.
                            </div>
                        @endif
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
