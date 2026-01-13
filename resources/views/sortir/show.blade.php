<x-app-layout>
    @php
        $upperOptions = $upperMaterials->map(function($m) {
            return [
                'id' => $m->id, 
                'name' => $m->name . ' (Sisa: ' . $m->stock . ' ' . $m->unit . ')', 
                'stock' => $m->stock,
                'disabled' => $m->stock <= 0
            ];
        })->values();

        $solOptions = $solMaterials->map(function($m) {
            $displayName = $m->name . ' - ' . ($m->size ?? 'All Size') . ' (' . $m->sub_category . ') (Sisa: ' . $m->stock . ')';
            return [
                'id' => $m->id, 
                'name' => $displayName,
                'sub_category' => $m->sub_category,
                'stock' => $m->stock,
                'disabled' => $m->stock <= 0
            ];
        })->values();
    @endphp

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
                                            <th class="px-6 py-3">Type / Kategori</th>
                                            <th class="px-6 py-3">Material</th>
                                            <th class="px-6 py-3">Size</th>
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
                                                    {{ $m->type == 'Material Sol' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $m->sub_category ?? 'Upper' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 font-bold text-gray-800">
                                                {{ $m->name }}
                                                <div class="text-[10px] text-gray-400 font-normal">{{ $m->type }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($m->size)
                                                    <span class="font-mono font-bold">{{ $m->size }}</span>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
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

                        <!-- Material Selection Section -->
                        <div class="bg-gradient-to-br from-gray-50 to-white border-t border-gray-100" 
                             x-data="{ 
                                activeTab: '{{ $suggestedTab }}', 
                                subCategoryFilter: 'all'
                             }">
                            <!-- Section Header -->
                            <div class="px-6 py-4 bg-gradient-to-r from-teal-600 to-teal-700 border-b-4 border-orange-400">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-bold text-white">Ambil Material dari Gudang</h4>
                                        <p class="text-xs text-teal-100">Pilih material yang dibutuhkan untuk order ini</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 space-y-6">
                                <!-- Tabs -->
                                <div class="flex space-x-2 bg-gradient-to-r from-gray-100 to-gray-50 p-1.5 rounded-xl shadow-inner border border-gray-200">
                                    <button @click="activeTab = 'upper'" 
                                            :class="{ 'bg-white text-teal-700 shadow-md scale-105': activeTab === 'upper', 'text-gray-500 hover:text-gray-700 hover:bg-white/50': activeTab !== 'upper' }"
                                            class="px-6 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 flex-1 text-center relative">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                            Upper
                                        </span>
                                        @if($suggestedTab === 'upper') 
                                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-teal-500 border-2 border-white"></span>
                                            </span> 
                                        @endif
                                    </button>
                                    <button @click="activeTab = 'sol'" 
                                            :class="{ 'bg-white text-orange-700 shadow-md scale-105': activeTab === 'sol', 'text-gray-500 hover:text-gray-700 hover:bg-white/50': activeTab !== 'sol' }"
                                            class="px-6 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 flex-1 text-center relative">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            Sol
                                        </span>
                                        @if($suggestedTab === 'sol') 
                                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500 border-2 border-white"></span>
                                            </span> 
                                        @endif
                                    </button>
                                </div>

                                <!-- Form Upper -->
                                <div x-show="activeTab === 'upper'" class="space-y-4">
                                    <div class="bg-white rounded-xl shadow-sm border border-teal-100 overflow-hidden">
                                        <div class="bg-gradient-to-r from-teal-50 to-teal-100 px-4 py-3 border-b border-teal-200">
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-sm font-bold text-teal-800 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                                                    Material Upper
                                                </h5>
                                                <span class="text-xs font-semibold text-teal-600 bg-teal-200 px-2 py-1 rounded-full">{{ count($upperOptions) }} items</span>
                                            </div>
                                        </div>
                                        <form action="{{ route('sortir.add-material', $order->id) }}" method="POST" class="p-4 space-y-3">
                                            @csrf
                                            
                                            <!-- Search Input -->
                                            <div class="relative">
                                                <input type="text" id="upperSearch" placeholder="🔍 Cari material upper..." 
                                                    class="block w-full text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-sm pl-4 pr-10 py-3"
                                                    onkeyup="filterUpperMaterials()">
                                                <button type="button" onclick="document.getElementById('upperSearch').value=''; filterUpperMaterials();" 
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-12 gap-3">
                                                <div class="col-span-12 sm:col-span-7">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Material</label>
                                                    <select name="material_id" id="upperMaterialSelect" required class="block w-full text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-sm py-3 px-4">
                                                        <option value="">-- Pilih Material Upper --</option>
                                                    </select>
                                                </div>
                                                <div class="col-span-6 sm:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Quantity</label>
                                                    <input name="quantity" type="number" min="1" value="1" required class="block w-full text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-sm text-center font-bold py-3" />
                                                </div>
                                                <div class="col-span-6 sm:col-span-3 flex items-end">
                                                    <button type="submit" class="w-full bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white py-3 rounded-xl shadow-md hover:shadow-lg font-bold text-sm transition-all transform hover:scale-105">
                                                        <span class="flex items-center justify-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                            AMBIL
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-gray-400 italic flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                                Material upper untuk perbaikan bagian atas sepatu
                                            </p>
                                        </form>
                                    </div>
                                </div>

                                <script>
                                    // Store original options for Upper
                                    const upperMaterialsData = @json($upperOptions);
                                    const upperSelect = document.getElementById('upperMaterialSelect');
                                    
                                    // Initial render for Upper
                                    function renderUpperMaterials(materials) {
                                        upperSelect.innerHTML = '<option value="">-- Pilih Material Upper --</option>';
                                        
                                        // Simple list (no grouping for upper since it doesn't have sub_category)
                                        materials.forEach(material => {
                                            const option = document.createElement('option');
                                            option.value = material.id;
                                            option.textContent = material.name;
                                            option.disabled = material.disabled;
                                            upperSelect.appendChild(option);
                                        });
                                    }
                                    
                                    // Filter function for Upper
                                    function filterUpperMaterials() {
                                        const searchTerm = document.getElementById('upperSearch').value.toLowerCase();
                                        const filtered = upperMaterialsData.filter(m => 
                                            m.name.toLowerCase().includes(searchTerm)
                                        );
                                        renderUpperMaterials(filtered);
                                    }
                                    
                                    // Initial render for Upper
                                    renderUpperMaterials(upperMaterialsData);
                                </script>

                                <!-- Form Sol -->
                                <div x-show="activeTab === 'sol'" class="space-y-4" style="display: none;">
                                    <div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
                                        <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-4 py-3 border-b border-orange-200">
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-sm font-bold text-orange-800 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                                                    Material Sol
                                                </h5>
                                                <span class="text-xs font-semibold text-orange-600 bg-orange-200 px-2 py-1 rounded-full">{{ count($solOptions) }} items</span>
                                            </div>
                                        </div>
                                        <form action="{{ route('sortir.add-material', $order->id) }}" method="POST" class="p-4 space-y-3">
                                            @csrf
                                            
                                            <!-- Search Input -->
                                            <div class="relative">
                                                <input type="text" id="solSearch" placeholder="🔍 Cari material sol..." 
                                                    class="block w-full text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm pl-4 pr-10 py-3"
                                                    onkeyup="filterSolMaterials()">
                                                <button type="button" onclick="document.getElementById('solSearch').value=''; filterSolMaterials();" 
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-600 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-12 gap-3">
                                                <div class="col-span-12 sm:col-span-7">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Material</label>
                                                    <select name="material_id" id="solMaterialSelect" required class="block w-full text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm py-3 px-4">
                                                        <option value="">-- Pilih Material Sol --</option>
                                                    </select>
                                                </div>
                                                <div class="col-span-6 sm:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Quantity</label>
                                                    <input name="quantity" type="number" min="1" value="1" required class="block w-full text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm text-center font-bold py-3" />
                                                </div>
                                                <div class="col-span-6 sm:col-span-3 flex items-end">
                                                    <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white py-3 rounded-xl shadow-md hover:shadow-lg font-bold text-sm transition-all transform hover:scale-105">
                                                        <span class="flex items-center justify-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                            AMBIL
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-gray-400 italic flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                                Material dikelompokkan berdasarkan kategori (Sol Potong, Sol Jadi, Foxing, Vibram)
                                            </p>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // Store original options
                                const solMaterialsData = @json($solOptions);
                                const solSelect = document.getElementById('solMaterialSelect');
                                
                                // Initial render
                                function renderSolMaterials(materials) {
                                    solSelect.innerHTML = '<option value="">-- Pilih Material Sol --</option>';
                                    
                                    // Group by category
                                    const grouped = materials.reduce((acc, m) => {
                                        if (!acc[m.sub_category]) acc[m.sub_category] = [];
                                        acc[m.sub_category].push(m);
                                        return acc;
                                    }, {});
                                    
                                    // Render grouped options
                                    Object.keys(grouped).forEach(category => {
                                        const optgroup = document.createElement('optgroup');
                                        optgroup.label = `📦 ${category}`;
                                        
                                        grouped[category].forEach(material => {
                                            const option = document.createElement('option');
                                            option.value = material.id;
                                            option.textContent = `${material.name} (Stock: ${material.stock})`;
                                            option.disabled = material.disabled;
                                            optgroup.appendChild(option);
                                        });
                                        
                                        solSelect.appendChild(optgroup);
                                    });
                                }
                                
                                // Filter function
                                function filterSolMaterials() {
                                    const searchTerm = document.getElementById('solSearch').value.toLowerCase();
                                    const filtered = solMaterialsData.filter(m => 
                                        m.name.toLowerCase().includes(searchTerm)
                                    );
                                    renderSolMaterials(filtered);
                                }
                                
                                // Initial render
                                renderSolMaterials(solMaterialsData);
                            </script>
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
                                <!-- Upper -->
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Upper</div>
                                @foreach($upperMaterials as $stock)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 hover:border-teal-200 transition-colors shadow-sm group">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700 transition-colors">{{ $stock->name }}</span>
                                        </div>
                                        <span class="text-xs font-mono font-bold px-2 py-1 rounded {{ $stock->stock > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $stock->stock }} {{ $stock->unit }}
                                        </span>
                                    </div>
                                @endforeach

                                <!-- Sol -->
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4">Sol</div>
                                @foreach($solMaterials as $stock)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 hover:border-teal-200 transition-colors shadow-sm group">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700 transition-colors">{{ $stock->name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $stock->size ? 'Size: '.$stock->size : $stock->sub_category }}</span>
                                        </div>
                                        <span class="text-xs font-mono font-bold px-2 py-1 rounded {{ $stock->stock > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $stock->stock }}
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
                        $hasSol = $order->materials->contains(fn($m) => $m->type === 'Material Sol');
                        $hasUpper = $order->materials->contains(fn($m) => $m->type === 'Material Upper');
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

            <!-- Upsell Section -->
            <div class="flex justify-start border-t border-gray-200 pt-6 mt-8" x-data="{ openUpsell: false }">
                <button @click="openUpsell = true" class="text-sm text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tambah Layanan (Upsell)
                </button>

                <!-- Model Upsell -->
                <div x-show="openUpsell" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;">
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative" @click.away="openUpsell = false">
                         <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Tambah Layanan (Upsell)</h3>
                            <button @click="openUpsell = false" class="text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                        
                        <p class="text-sm text-gray-500 mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                            Order akan dikembalikan ke status <strong>PREPARATION</strong> untuk pengerjaan ulang. <br>
                            Material lama tetap tersimpan. Input material baru nanti setelah order masuk lagi ke Sortir.
                        </p>

                        <form action="{{ route('sortir.add-service', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-5">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Layanan Tambahan</label>
                                <select name="service_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5" required>
                                    <option value="">-- Cari Layanan --</option>
                                    @foreach(\App\Models\Service::orderBy('name')->get() as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->category }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Kondisi (Opsional)</label>
                                <label class="block mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition-colors cursor-pointer bg-white">
                                    <div class="space-y-1 text-center w-full">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium text-blue-600 hover:text-blue-500">Upload Foto</span>
                                            <span class="pl-1">atau drag and drop</span>
                                            <input id="upsell-photo-sortir" name="upsell_photo" type="file" class="sr-only" accept="image/*" onchange="document.getElementById('file-chosen-sortir').textContent = this.files[0].name">
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                        <p id="file-chosen-sortir" class="text-xs font-bold text-teal-600 pt-2"></p>
                                    </div>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="openUpsell = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-bold">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md">Simpan & Proses</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
