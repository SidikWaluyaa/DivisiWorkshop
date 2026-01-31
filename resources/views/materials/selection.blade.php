<x-app-layout>
    <style>
        :root {
            --primary-green: #22AF85;
            --accent-yellow: #FFC232;
            --bg-premium: #F9FAFB;
            --text-dark: #1F2937;
        }
        .btn-premium-yellow {
            background-color: var(--accent-yellow);
            color: var(--text-dark);
            font-weight: 700;
            transition: all 0.2s;
        }
        .btn-premium-yellow:hover {
            background-color: #e5ae2d;
            transform: translateY(-1px);
        }
        .text-premium-green { color: var(--primary-green); }
        .bg-premium-green { background-color: var(--primary-green); }
        .border-premium-green { border-color: var(--primary-green); }
        .focus-premium-green:focus { border-color: var(--primary-green) !important; ring-color: var(--primary-green) !important; }
    </style>

    <div class="min-h-screen bg-gray-50 pb-12" x-data="materialSelection()">
        <!-- Header Section -->
        <div class="bg-premium-green border-b-4 border-accent-yellow shadow-lg mb-8">
            <div class="container mx-auto px-6 py-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md text-white border border-white/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Pilih Material</h1>
                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                <span class="px-3 py-1 bg-accent-yellow text-text-dark text-xs font-bold rounded-full shadow-sm">{{ $workOrder->spk_number }}</span>
                                <span class="text-white/80 text-sm font-medium">Customer: <span class="text-white font-bold">{{ $workOrder->customer->name ?? 'N/A' }}</span></span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('sortir.show', $workOrder->id) }}" class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl backdrop-blur-sm transition-all border border-white/30 font-bold text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Form Card -->
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-2 h-6 bg-premium-green rounded-full"></span>
                                Rincian Kebutuhan Material
                            </h2>
                        </div>
                        
                        <div class="p-8">
                            <form action="{{ route('materials.selection.store', $workOrder->id) }}" method="POST" id="materialForm">
                                @csrf
                                
                                <!-- Material List -->
                                <div class="space-y-6">
                                    <template x-for="(item, index) in items" :key="item.id">
                                        <div class="bg-white p-6 rounded-2xl relative group border-2 border-gray-100 hover:border-premium-green transition-all shadow-sm">
                                            <button type="button" @click="removeItem(index)" class="absolute -top-3 -right-3 w-8 h-8 bg-white text-red-500 rounded-full shadow-md border border-red-50 hover:bg-red-50 flex items-center justify-center transition-all z-10">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                                                <!-- Select Material -->
                                                <div class="md:col-span-6">
                                                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-widest pl-1">Material</label>
                                                    <select :name="'materials['+index+'][material_id]'" x-model="item.material_id" @change="updateMaterialInfo(index)" class="w-full rounded-xl border-gray-200 py-3 shadow-sm focus:border-premium-green focus:ring-0 text-gray-800 font-semibold bg-gray-50/50">
                                                        <option value="">-- Pilih Material --</option>
                                                        @foreach($materials as $category => $list)
                                                            <optgroup label="{{ $category }}">
                                                                @foreach($list as $m)
                                                                    <option value="{{ $m->id }}" 
                                                                        data-unit="{{ $m->unit }}"
                                                                        data-stock="{{ $m->getAvailableStock() }}"
                                                                        data-category="{{ $m->category }}">
                                                                        {{ $m->name }} (Stok: {{ $m->getAvailableStock() }} {{ $m->unit }})
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>
            
                                                <!-- Quantity -->
                                                <div class="md:col-span-3">
                                                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-widest pl-1">
                                                        Jumlah <span x-text="item.unit ? '('+item.unit+')' : ''" class="text-premium-green lowercase italic"></span>
                                                    </label>
                                                    <input type="number" step="0.1" :name="'materials['+index+'][quantity]'" x-model="item.quantity" class="w-full rounded-xl border-gray-200 py-3 text-center font-bold text-gray-800 bg-gray-50/50 focus:border-premium-green focus:ring-0" placeholder="0">
                                                </div>
            
                                                <!-- Info Shortage -->
                                                <div class="md:col-span-3 pb-3">
                                                    <div class="p-3 rounded-xl border flex items-center justify-center bg-gray-50" x-show="item.material_id">
                                                        <template x-if="isProduction(item) && checkShortage(item)">
                                                            <span class="text-red-600 font-bold text-xs flex items-center">
                                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                Kurang: <span x-text="(item.quantity - item.available_stock).toFixed(1)"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="isProduction(item) && !checkShortage(item)">
                                                            <span class="text-premium-green font-bold text-xs flex items-center">
                                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                Stok Aman
                                                            </span>
                                                        </template>
                                                        <template x-if="!isProduction(item)">
                                                            <span class="text-blue-600 font-bold text-xs flex items-center">
                                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                Budget Request
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                                
                                                <!-- Notes row -->
                                                <div class="md:col-span-12">
                                                    <input type="text" :name="'materials['+index+'][notes]'" x-model="item.notes" class="w-full text-sm font-medium rounded-xl border-gray-100 py-3 px-4 bg-gray-50/30 placeholder-gray-400 focus:border-premium-green focus:ring-0" placeholder="Catatan tambahan (opsional)">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
            
                                <!-- Add Button -->
                                <div class="mt-6">
                                    <button type="button" @click="addItem()" class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-premium-green text-premium-green rounded-xl font-bold hover:bg-green-50 transition-all shadow-sm text-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Tambah Material
                                    </button>
                                </div>
            
                                <!-- Global Notes -->
                                <div class="mt-10 p-6 bg-gray-50 rounded-[1.5rem] border border-gray-100">
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-3 tracking-widest">Catatan Proses / Deskripsi Request</label>
                                    <textarea name="notes" rows="3" class="w-full rounded-2xl border-gray-200 p-4 font-medium text-gray-800 bg-white shadow-sm focus:border-premium-green focus:ring-0" placeholder="Contoh: Request sol warna putih karena sisa stok hanya warna hitam..."></textarea>
                                </div>
            
                                <!-- Submit Actions -->
                                <div class="mt-10 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-end items-center gap-4">
                                    <a href="{{ route('sortir.show', $workOrder->id) }}" class="w-full md:w-auto px-8 py-4 text-gray-500 hover:text-gray-800 font-bold transition-all order-2 md:order-1">Batal</a>
                                    <button type="submit" class="w-full md:w-auto px-10 py-4 btn-premium-yellow text-text-dark rounded-2xl shadow-xl transform hover:-translate-y-1 active:translate-y-0 transition-all font-bold uppercase tracking-widest text-sm order-1 md:order-2">
                                        <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Proses Material
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Recommendations -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
                        <div class="p-6 bg-premium-green text-white">
                            <h3 class="font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 text-accent-yellow" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 110-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path></svg>
                                Rekomendasi Material
                            </h3>
                            <p class="text-[10px] text-white/70 uppercase tracking-widest mt-1">Sesuai Kebutuhan SPK</p>
                        </div>
                        <div class="p-6">
                            @if(count($recommendedMaterials) > 0)
                                <div class="space-y-3">
                                    @foreach($recommendedMaterials as $rmat)
                                        <button type="button" 
                                            @click="addRecommended({{ $rmat->id }}, '{{ $rmat->name }}', '{{ $rmat->unit }}', {{ $rmat->getAvailableStock() }}, '{{ $rmat->category }}')"
                                            class="w-full flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-premium-green hover:bg-green-50/50 transition-all text-left shadow-sm group">
                                            <div>
                                                <p class="text-sm font-bold text-gray-700 group-hover:text-premium-green transition-colors">{{ $rmat->name }}</p>
                                                <p class="text-[10px] text-gray-400 capitalize">{{ $rmat->type }} • Stok: {{ $rmat->getAvailableStock() }}</p>
                                            </div>
                                            <div class="p-1.5 bg-gray-50 rounded-lg group-hover:bg-premium-green group-hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <p class="text-xs text-gray-400 italic">Pilih material di sebelah kiri</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Summary Info -->
                    <div class="bg-white/60 backdrop-blur-sm border-2 border-premium-green/10 rounded-[2rem] p-6 text-xs shadow-sm">
                        <h4 class="font-extrabold text-premium-green flex items-center gap-2 mb-3 text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Sistem Cerdas
                        </h4>
                        <div class="space-y-3">
                            <div class="bg-white/80 p-3 rounded-xl border border-gray-50">
                                <p class="font-bold text-gray-700 mb-1">Auto-Suggest</p>
                                <p class="text-gray-400 leading-tight">Material di atas dipilih otomatis berdasarkan servis yang dikerjakan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function materialSelection() {
            return {
                items: @json($existingMaterials ?? []),
                
                init() {
                    if (this.items.length === 0) {
                        this.addItem();
                    }
                },
                
                addItem() {
                    this.items.push({
                        id: Date.now(),
                        material_id: '',
                        quantity: '',
                        unit: '',
                        available_stock: 0,
                        category: '',
                        notes: ''
                    });
                },

                addRecommended(id, name, unit, stock, category) {
                    // Check if already in items and empty
                    let emptyIndex = this.items.findIndex(i => !i.material_id);
                    
                    if (emptyIndex !== -1) {
                        this.items[emptyIndex].material_id = id;
                        this.items[emptyIndex].unit = unit;
                        this.items[emptyIndex].available_stock = stock;
                        this.items[emptyIndex].category = category;
                        this.items[emptyIndex].notes = '';
                    } else {
                        this.items.push({
                            id: Date.now(),
                            material_id: id,
                            quantity: 1, // Default quantity 1 for recommended
                            unit: unit,
                            available_stock: stock,
                            category: category,
                            notes: ''
                        });
                    }
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    } else {
                        // Reset first item if it's the only one
                        this.items[0].material_id = '';
                        this.items[0].quantity = '';
                        this.items[0].unit = '';
                        this.items[0].available_stock = 0;
                        this.items[0].category = '';
                        this.items[0].notes = '';
                    }
                },
                
                updateMaterialInfo(index) {
                    // Use index to target the correct element
                    let select = document.getElementsByName('materials['+index+'][material_id]')[0];
                    if (!select) return;

                    let selectedOption = select.options[select.selectedIndex];
                    
                    if (selectedOption && selectedOption.value) {
                        this.items[index].unit = selectedOption.dataset.unit || '';
                        this.items[index].available_stock = parseFloat(selectedOption.dataset.stock) || 0;
                        this.items[index].category = selectedOption.dataset.category || '';
                    } else {
                        this.items[index].unit = '';
                        this.items[index].available_stock = 0;
                        this.items[index].category = '';
                    }
                },
    
                isProduction(item) {
                    return item.category === 'PRODUCTION';
                },
    
                checkShortage(item) {
                    if (!item.quantity) return false;
                    return parseFloat(item.quantity) > item.available_stock;
                }
            }
        }
    </script>
</x-app-layout>

